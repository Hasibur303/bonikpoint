<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items', 'user')]);
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
}
