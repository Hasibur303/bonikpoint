<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
        ]);

        $order->update([
            ...$data,
            'delivery_charge_payment_option' => 'pay_now',
            'status' => 'pending',
        ]);

        return redirect()->route('orders.show', $order)->with('success', 'Delivery charge payment submitted. Admin will review and confirm your order.');
    }

    private function authorizeDeliveryPayment(Order $order): void
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later', 404);
    }
}
