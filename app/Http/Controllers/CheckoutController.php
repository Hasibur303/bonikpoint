<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $cartItems = app(CartController::class)->items();

        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.create', [
            'cartItems' => $cartItems,
            'subtotal' => CartController::subtotal(),
            'shipping' => $this->deliveryCharge(old('delivery_area', 'inside_dhaka')),
            'advanceDeliveryRequired' => $this->advanceDeliveryRequired($cartItems),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cartItems = app(CartController::class)->items();

        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $advanceDeliveryRequired = $this->advanceDeliveryRequired($cartItems);

        $rules = [
            'customer_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($advanceDeliveryRequired) {
            $rules = [
                ...$rules,
                'delivery_area' => ['required', 'in:inside_dhaka,outside_dhaka'],
                'delivery_charge_payment_option' => ['required', 'in:pay_now,pay_later'],
                'delivery_payment_method' => ['required_if:delivery_charge_payment_option,pay_now', 'nullable', 'in:Bkash,Nagad,Rocket'],
                'delivery_payment_mobile' => ['required_if:delivery_charge_payment_option,pay_now', 'nullable', 'string', 'max:30'],
                'delivery_transaction_id' => ['required_if:delivery_charge_payment_option,pay_now', 'nullable', 'string', 'max:120'],
            ];
        }

        $data = $request->validate($rules);

        $order = DB::transaction(function () use ($data, $cartItems, $advanceDeliveryRequired) {
            $subtotal = collect($cartItems)->sum('total');
            $shipping = $advanceDeliveryRequired ? $this->deliveryCharge($data['delivery_area']) : 0;
            $order = Order::create([
                ...$data,
                'user_id' => auth()->id(),
                'order_number' => 'BP-'.now()->format('YmdHis').'-'.auth()->id(),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
                'advance_delivery_required' => $advanceDeliveryRequired,
                'delivery_area' => $advanceDeliveryRequired ? $data['delivery_area'] : null,
                'delivery_charge_payment_option' => $advanceDeliveryRequired ? $data['delivery_charge_payment_option'] : null,
                'delivery_payment_method' => $advanceDeliveryRequired && $data['delivery_charge_payment_option'] === 'pay_now' ? $data['delivery_payment_method'] : null,
                'delivery_payment_mobile' => $advanceDeliveryRequired && $data['delivery_charge_payment_option'] === 'pay_now' ? $data['delivery_payment_mobile'] : null,
                'delivery_transaction_id' => $advanceDeliveryRequired && $data['delivery_charge_payment_option'] === 'pay_now' ? $data['delivery_transaction_id'] : null,
            ]);

            foreach ($cartItems as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'buying_price' => $product->buying_price,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $quantity,
                    'total' => $item['total'],
                ]);

                $product->decrement('stock', $quantity);
            }

            return $order;
        });

        session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully.');
    }

    private function advanceDeliveryRequired(array $cartItems): bool
    {
        return collect($cartItems)->contains(fn ($item) => (bool) $item['product']->advance_delivery_charge);
    }

    private function deliveryCharge(?string $area): int
    {
        return $area === 'outside_dhaka' ? 120 : 60;
    }
}
