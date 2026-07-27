<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\SteadfastCourier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class OrderController extends Controller
{
    private const STATUSES = [
        'waiting_delivery_charge',
        'pending',
        'confirmed',
        'processing',
        'delivered',
        'cancelled',
    ];

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = trim((string) $request->input('search'));
        $statusCounts = Order::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        if (! in_array($status, self::STATUSES, true)) {
            $status = null;
        }

        return view('admin.orders.index', [
            'orders' => Order::with('user')
                ->when($status, fn ($query) => $query->where('status', $status))
                ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                    $query->where('parcel_id', 'like', '%'.$search.'%')
                        ->orWhere('steadfast_consignment_id', 'like', '%'.$search.'%')
                        ->orWhere('steadfast_tracking_code', 'like', '%'.$search.'%')
                        ->orWhere('order_number', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('mobile', 'like', '%'.$search.'%');
                }))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'selectedStatus' => $status,
            'statuses' => self::STATUSES,
            'statusCounts' => $statusCounts,
            'allOrdersCount' => $statusCounts->sum(),
            'search' => $search,
        ]);
    }

    public function show(Order $order, SteadfastCourier $steadfast): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('items', 'user'),
            'steadfastConfigured' => $steadfast->configured(),
        ]);
    }

    public function receipt(Order $order): View
    {
        return view('orders.receipt', [
            'order' => $order->load('items'),
            'adminReceipt' => true,
        ]);
    }

    public function paymentProof(Order $order): StreamedResponse
    {
        abort_unless($order->delivery_payment_proof && Storage::disk('local')->exists($order->delivery_payment_proof), 404);

        return Storage::disk('local')->response(
            $order->delivery_payment_proof,
            'payment-proof-'.$order->order_number.'.'.pathinfo($order->delivery_payment_proof, PATHINFO_EXTENSION),
            ['Cache-Control' => 'private, no-store']
        );
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:waiting_delivery_charge,pending,confirmed,processing,delivered,cancelled'],
            'parcel_id' => ['nullable', 'string', 'max:120', Rule::unique('orders', 'parcel_id')->ignore($order)],
            'delivery_payment_proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cancellation_note' => ['nullable', 'string', 'max:1000', 'required_if:status,cancelled'],
        ]);

        $validated['parcel_id'] = filled($validated['parcel_id'] ?? null)
            ? trim($validated['parcel_id'])
            : null;
        $validated['cancellation_note'] = filled($validated['cancellation_note'] ?? null)
            ? trim($validated['cancellation_note'])
            : null;

        if ($order->hasSteadfastShipment()) {
            $validated['parcel_id'] = $order->parcel_id;
        }

        $adminRecordedDeliveryPayment = $request->hasFile('delivery_payment_proof')
            && $order->advance_delivery_required
            && $validated['status'] !== 'cancelled';

        if ($request->hasFile('delivery_payment_proof')) {
            $validated['delivery_payment_proof'] = $request->file('delivery_payment_proof')
                ->store('delivery-payment-proofs', 'local');
        } else {
            unset($validated['delivery_payment_proof']);
        }

        if ($adminRecordedDeliveryPayment) {
            $validated['delivery_charge_payment_option'] = 'pay_now';
            $validated['delivery_payment_method'] = $order->delivery_payment_method ?: 'Admin recorded';

            if ($order->status === 'waiting_delivery_charge' && $validated['status'] === 'waiting_delivery_charge') {
                $validated['status'] = 'pending';
            }
        }

        DB::transaction(function () use ($order, $validated) {
            $order->loadMissing('items');
            $previousStatus = $order->status;
            $nextStatus = $validated['status'];

            if ($previousStatus === 'cancelled' && $nextStatus !== 'cancelled') {
                throw ValidationException::withMessages([
                    'status' => 'Cancelled orders cannot be moved back to active status. Create a new order instead.',
                ]);
            }

            if ($previousStatus !== 'cancelled' && $nextStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)
                            ->lockForUpdate()
                            ->increment('stock', $item->quantity);
                    }
                }
            }

            $order->update($validated);
        });

        return back()->with('success', $adminRecordedDeliveryPayment
            ? 'Payment screenshot saved. Delivery charge marked paid and the order is ready for review.'
            : 'Order fulfillment details updated.');
    }

    public function sendToSteadfast(Order $order, SteadfastCourier $steadfast): RedirectResponse
    {
        $lock = Cache::lock('steadfast-order-'.$order->getKey(), 30);

        if (! $lock->get()) {
            return back()->withErrors(['steadfast' => 'This order is already being submitted. Please wait a moment and refresh the page.']);
        }

        try {
            $order->refresh()->loadMissing('items');

            if ($order->is_offline_sale) {
                return back()->withErrors(['steadfast' => 'Offline sales cannot be submitted to Steadfast.']);
            }

            if ($order->hasSteadfastShipment()) {
                return back()->withErrors(['steadfast' => 'This order has already been submitted to Steadfast.']);
            }

            if ($order->status !== 'confirmed') {
                return back()->withErrors(['steadfast' => 'Confirm the order before sending it to Steadfast.']);
            }

            try {
                $shipment = $steadfast->createOrder($order);

                $order->update([
                    'parcel_id' => $shipment['consignment_id'],
                    'steadfast_consignment_id' => $shipment['consignment_id'],
                    'steadfast_tracking_code' => $shipment['tracking_code'],
                    'steadfast_status' => $shipment['status'],
                    'steadfast_cod_amount' => $order->dueAmount(),
                    'steadfast_submitted_at' => now(),
                    'steadfast_last_synced_at' => now(),
                    'steadfast_last_error' => null,
                    'status' => 'processing',
                ]);
            } catch (Throwable $exception) {
                report($exception);

                $order->update([
                    'steadfast_last_error' => $exception->getMessage(),
                ]);

                return back()->withErrors([
                    'steadfast' => 'Steadfast submission failed: '.$exception->getMessage(),
                ]);
            }

            return back()->with('success', 'Parcel submitted to Steadfast successfully. Consignment ID: '.$shipment['consignment_id']);
        } finally {
            $lock->release();
        }
    }

    public function refreshSteadfastStatus(Order $order, SteadfastCourier $steadfast): RedirectResponse
    {
        if (! $order->hasSteadfastShipment()) {
            return back()->withErrors(['steadfast' => 'This order has not been submitted to Steadfast yet.']);
        }

        try {
            $status = $steadfast->status($order->steadfast_consignment_id);

            $order->update([
                'steadfast_status' => $status,
                'steadfast_last_synced_at' => now(),
                'steadfast_last_error' => null,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            $order->update([
                'steadfast_last_error' => $exception->getMessage(),
            ]);

            return back()->withErrors([
                'steadfast' => 'Could not refresh Steadfast status: '.$exception->getMessage(),
            ]);
        }

        return back()->with('success', 'Steadfast parcel status updated: '.str($status)->replace('_', ' ')->title());
    }
}
