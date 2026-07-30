<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\SteadfastCourier;
use App\Services\SteadfastOrderSynchronizer;
use App\Support\OrderAdjustmentCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;
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
            'order' => $order->load('items', 'user', 'adjustedBy'),
            'steadfastConfigured' => $steadfast->configured(),
        ]);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_ids' => ['required', 'array', 'min:1', 'max:100'],
            'order_ids.*' => ['required', 'integer', 'distinct', 'exists:orders,id'],
        ], [
            'order_ids.required' => 'Select at least one order to delete.',
        ]);

        $orderIds = collect($validated['order_ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $paymentProofs = [];
        $deletedOrders = [];

        DB::transaction(function () use ($orderIds, &$paymentProofs, &$deletedOrders): void {
            $orders = Order::query()
                ->with('items')
                ->whereKey($orderIds)
                ->lockForUpdate()
                ->get();

            if ($orders->count() !== $orderIds->count()) {
                throw ValidationException::withMessages([
                    'order_ids' => 'One or more selected orders no longer exist. Refresh the page and try again.',
                ]);
            }

            $activeParcel = $orders->first(fn (Order $order) => $order->hasActiveSteadfastShipment());

            if ($activeParcel) {
                throw ValidationException::withMessages([
                    'order_ids' => "Order {$activeParcel->order_number} has an active Steadfast parcel. Cancel the courier parcel and mark the order cancelled before deleting it.",
                ]);
            }

            foreach ($orders as $order) {
                if ($order->shouldRestoreStockWhenDeleted()) {
                    foreach ($order->items as $item) {
                        if ($item->product_id) {
                            Product::whereKey($item->product_id)
                                ->lockForUpdate()
                                ->increment('stock', $item->quantity);
                        }
                    }
                }

                if ($order->delivery_payment_proof) {
                    $paymentProofs[] = $order->delivery_payment_proof;
                }

                $deletedOrders[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                ];

                $order->delete();
            }
        });

        foreach ($deletedOrders as $deletedOrder) {
            Log::warning('Order permanently deleted by administrator.', [
                ...$deletedOrder,
                'admin_id' => auth()->id(),
            ]);
        }

        if ($paymentProofs) {
            Storage::disk('local')->delete($paymentProofs);
        }

        return back()->with('success', $orderIds->count().' selected '.str('order')->plural($orderIds->count()).' permanently deleted.');
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

    public function updateAdjustment(Request $request, Order $order, OrderAdjustmentCalculator $calculator): RedirectResponse
    {
        $validated = $request->validate([
            'adjustment_type' => ['required', Rule::in(['fixed_discount', 'percentage_discount', 'extra_charge'])],
            'adjustment_value' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'adjustment_reason' => ['required', 'string', 'max:255'],
            'adjustment_note' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($order, $validated, $calculator) {
            $lockedOrder = Order::whereKey($order->getKey())->lockForUpdate()->firstOrFail();
            $this->ensureAdjustmentCanChange($lockedOrder);

            $value = round((float) $validated['adjustment_value'], 2);
            try {
                $amounts = $calculator->calculate(
                    (float) $lockedOrder->subtotal,
                    (float) $lockedOrder->shipping,
                    $validated['adjustment_type'],
                    $value
                );
            } catch (InvalidArgumentException $exception) {
                throw ValidationException::withMessages([
                    'adjustment_value' => $exception->getMessage(),
                ]);
            }

            $previous = $lockedOrder->only([
                'total',
                'adjustment_type',
                'adjustment_value',
                'discount_amount',
                'extra_charge_amount',
                'adjustment_reason',
                'adjustment_note',
                'adjusted_by',
                'adjusted_at',
            ]);

            $lockedOrder->update([
                'total' => $amounts['total'],
                'adjustment_type' => $validated['adjustment_type'],
                'adjustment_value' => $value,
                'discount_amount' => $amounts['discount_amount'],
                'extra_charge_amount' => $amounts['extra_charge_amount'],
                'adjustment_reason' => trim($validated['adjustment_reason']),
                'adjustment_note' => filled($validated['adjustment_note'] ?? null)
                    ? trim($validated['adjustment_note'])
                    : null,
                'adjusted_by' => auth()->id(),
                'adjusted_at' => now(),
            ]);

            Log::notice('Order adjustment updated.', [
                'order_id' => $lockedOrder->id,
                'order_number' => $lockedOrder->order_number,
                'admin_id' => auth()->id(),
                'previous' => $previous,
                'current' => $lockedOrder->only([
                    'total',
                    'adjustment_type',
                    'adjustment_value',
                    'discount_amount',
                    'extra_charge_amount',
                    'adjustment_reason',
                    'adjustment_note',
                ]),
            ]);
        });

        return back()->with('success', 'Order adjustment applied and payable totals recalculated.');
    }

    public function clearAdjustment(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'clear_reason' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($order, $validated) {
            $lockedOrder = Order::whereKey($order->getKey())->lockForUpdate()->firstOrFail();
            $this->ensureAdjustmentCanChange($lockedOrder);

            if (! $lockedOrder->hasAdjustment()) {
                throw ValidationException::withMessages([
                    'clear_reason' => 'This order does not have an adjustment to remove.',
                ]);
            }

            $previous = $lockedOrder->only([
                'total',
                'adjustment_type',
                'adjustment_value',
                'discount_amount',
                'extra_charge_amount',
                'adjustment_reason',
                'adjustment_note',
                'adjusted_by',
                'adjusted_at',
            ]);

            $lockedOrder->update([
                'total' => $lockedOrder->originalTotal(),
                'adjustment_type' => null,
                'adjustment_value' => 0,
                'discount_amount' => 0,
                'extra_charge_amount' => 0,
                'adjustment_reason' => null,
                'adjustment_note' => null,
                'adjusted_by' => null,
                'adjusted_at' => null,
            ]);

            Log::notice('Order adjustment removed.', [
                'order_id' => $lockedOrder->id,
                'order_number' => $lockedOrder->order_number,
                'admin_id' => auth()->id(),
                'reason' => trim($validated['clear_reason']),
                'previous' => $previous,
            ]);
        });

        return back()->with('success', 'Order adjustment removed and the original total restored.');
    }

    public function sendToSteadfast(Order $order, SteadfastCourier $steadfast): RedirectResponse
    {
        $lock = Cache::lock('steadfast-order-'.$order->getKey(), 30);

        if (! $lock->get()) {
            return back()->withErrors(['steadfast' => 'This order is already being submitted. Please wait a moment and refresh the page.']);
        }

        try {
            $order->refresh()->loadMissing('items');

            if ($order->is_offline_sale && ! $order->requires_courier) {
                return back()->withErrors(['steadfast' => 'This offline sale was saved as a store-counter sale without courier delivery.']);
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

    public function refreshSteadfastStatus(Order $order, SteadfastOrderSynchronizer $synchronizer): RedirectResponse
    {
        if (! $order->hasSteadfastShipment()) {
            return back()->withErrors(['steadfast' => 'This order has not been submitted to Steadfast yet.']);
        }

        try {
            $previousStatus = $order->status;
            $status = $synchronizer->sync($order);
            $order->refresh();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'steadfast' => 'Could not refresh Steadfast status: '.$exception->getMessage(),
            ]);
        }

        $message = 'Steadfast parcel status updated: '.str($status)->replace('_', ' ')->title().'.';

        if ($previousStatus !== 'delivered' && $order->status === 'delivered') {
            $message .= ' Store order marked Delivered automatically.';
        }

        return back()->with('success', $message);
    }

    private function ensureAdjustmentCanChange(Order $order): void
    {
        if (in_array($order->status, ['delivered', 'cancelled'], true)) {
            throw ValidationException::withMessages([
                'adjustment_value' => 'Delivered or cancelled orders cannot be adjusted.',
            ]);
        }

        if ($order->hasSteadfastShipment()) {
            throw ValidationException::withMessages([
                'adjustment_value' => 'This parcel is already submitted to Steadfast. Adjustments are locked to prevent a COD mismatch.',
            ]);
        }
    }
}
