<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('cart.index', ['cartItems' => $this->items()]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_active, 404);

        $quantity = max(1, min((int) $request->input('quantity', 1), $product->stock));
        $cart = session('cart', []);
        $currentQuantity = $cart[$product->id]['quantity'] ?? 0;

        $cart[$product->id] = [
            'quantity' => min($currentQuantity + $quantity, $product->stock),
        ];

        session(['cart' => $cart]);

        return back()->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1']]);

        $cart = session('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = min((int) $request->quantity, $product->stock);
            session(['cart' => $cart]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Product removed from cart.');
    }

    public static function count(): int
    {
        return collect(session('cart', []))->sum('quantity');
    }

    public static function subtotal(): float
    {
        return collect((new self())->items())->sum(fn ($item) => $item['total']);
    }

    public function items(): array
    {
        $cart = session('cart', []);
        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)->map(function ($item, $productId) use ($products) {
            $product = $products->get((int) $productId);

            if (! $product) {
                return null;
            }

            $quantity = min((int) $item['quantity'], max(1, $product->stock));

            return [
                'product' => $product,
                'quantity' => $quantity,
                'total' => (float) $product->price * $quantity,
            ];
        })->filter()->values()->all();
    }
}
