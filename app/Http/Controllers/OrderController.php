<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('orders.index', [
            'orders' => auth()->user()->orders()->latest()->paginate(10),
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('orders.show', ['order' => $order->load('items.product', 'reviews')]);
    }

    public function receipt(Order $order): View
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('orders.receipt', ['order' => $order->load('items')]);
    }

    public function guestShow(Order $order, string $token): View
    {
        $this->authorizeGuestOrder($order, $token);

        return view('orders.guest-show', ['order' => $order->load('items.product')]);
    }

    public function guestReceipt(Order $order, string $token): View
    {
        $this->authorizeGuestOrder($order, $token);

        return view('orders.receipt', ['order' => $order->load('items')]);
    }

    public function deliveryPayment(Order $order): View
    {
        $this->authorizeDeliveryPayment($order);

        return view('orders.delivery-payment', [
            'order' => $order,
            'settings' => StoreSetting::deliverySettings(),
        ]);
    }

    public function updateDeliveryPayment(Request $request, Order $order): RedirectResponse
    {
        $this->authorizeDeliveryPayment($order);

        $data = $request->validate([
            'delivery_payment_method' => ['required', 'in:Bkash,Nagad,Rocket'],
            'delivery_payment_mobile' => ['required', 'string', 'max:30'],
            'delivery_transaction_id' => ['required', 'string', 'max:120'],
            'delivery_payment_proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $paymentProofPath = $request->hasFile('delivery_payment_proof')
            ? $request->file('delivery_payment_proof')->store('delivery-payment-proofs', 'local')
            : null;

        try {
            $order->update([
                ...$data,
                'delivery_payment_proof' => $paymentProofPath,
                'delivery_charge_payment_option' => 'pay_now',
                'status' => 'pending',
            ]);
        } catch (Throwable $exception) {
            if ($paymentProofPath) {
                Storage::disk('local')->delete($paymentProofPath);
            }

            throw $exception;
        }

        return redirect()->route('orders.show', $order)->with('success', 'Delivery charge payment submitted. Admin will review and confirm your order.');
    }

    private function authorizeDeliveryPayment(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later', 404);
    }

    private function authorizeGuestOrder(Order $order, string $token): void
    {
        abort_unless($order->user_id === null && $order->guest_token && hash_equals($order->guest_token, $token), 403);
    }
}
