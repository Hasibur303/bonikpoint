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
            'shipping' => 0,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cartItems = app(CartController::class)->items();

        if (count($cartItems) === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'mobile' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order = DB::transaction(function () use ($data, $cartItems) {
            $subtotal = collect($cartItems)->sum('total');
            $order = Order::create([
                ...$data,
                'user_id' => auth()->id(),
                'order_number' => 'BP-'.now()->format('YmdHis').'-'.auth()->id(),
                'subtotal' => $subtotal,
                'shipping' => 0,
                'total' => $subtotal,
            ]);

            foreach ($cartItems as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'buying_price' => $product->buying_price,
                    'unit_price' => $product->price,
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
}
