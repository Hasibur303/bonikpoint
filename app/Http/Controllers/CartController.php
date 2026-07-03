<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        return view('cart.index', ['cartItems' => $this->items()]);
    }

    public function store(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        abort_unless($product->is_active, 404);

        $quantity = max(1, min((int) $request->input('quantity', 1), $product->stock));
        $cart = session('cart', []);
        $currentQuantity = $cart[$product->id]['quantity'] ?? 0;

        $cart[$product->id] = [
            'quantity' => min($currentQuantity + $quantity, $product->stock),
        ];

        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "{$product->name} added to cart.",
                'cart' => $this->snapshot(),
            ]);
        }

        return back()->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1']]);

        $cart = session('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = min((int) $request->quantity, $product->stock);
            session(['cart' => $cart]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cart updated.',
                'cart' => $this->snapshot(),
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Product removed from cart.',
                'cart' => $this->snapshot(),
            ]);
        }

        return back()->with('success', 'Product removed from cart.');
    }

    public function snapshotResponse(): JsonResponse
    {
        return response()->json(['cart' => $this->snapshot()]);
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

    public function snapshot(): array
    {
        $items = collect($this->items())->map(fn ($item) => [
            'id' => $item['product']->id,
            'name' => $item['product']->name,
            'image' => $item['product']->image_url,
            'price' => (float) $item['product']->price,
            'quantity' => $item['quantity'],
            'stock' => $item['product']->stock,
            'total' => $item['total'],
        ])->values();

        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'subtotal' => $items->sum('total'),
        ];
    }
}
