<?php

namespace App\Http\Controllers;

use App\Models\Festival;
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
        $festival = $this->festivalFor($request, $product);
        $cartKey = $festival ? $this->cartKey($product->id, $festival->id) : (string) $product->id;
        $currentQuantity = $cart[$cartKey]['quantity'] ?? 0;
        $otherQuantity = $this->quantityForProduct($cart, $product->id, $cartKey);
        $allowedQuantity = max(0, $product->stock - $otherQuantity);

        if ($allowedQuantity < 1) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No more stock available for this product.',
                    'cart' => $this->snapshot(),
                ], 422);
            }

            return back()->with('error', 'No more stock available for this product.');
        }

        $cart[$cartKey] = [
            'quantity' => min($currentQuantity + $quantity, $allowedQuantity),
            'festival_id' => $festival?->id,
            'unit_price' => $festival ? $festival->discountedPrice($product) : (float) $product->price,
            'festival_title' => $festival?->title,
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

        $cartKey = $request->input('cart_key', (string) $product->id);

        if (isset($cart[$cartKey])) {
            $otherQuantity = $this->quantityForProduct($cart, $product->id, $cartKey);
            $cart[$cartKey]['quantity'] = min((int) $request->quantity, max(1, $product->stock - $otherQuantity));
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
        $cartKey = $request->input('cart_key', (string) $product->id);
        unset($cart[$cartKey]);
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
        $productIds = collect(array_keys($cart))->map(fn ($key) => (int) str($key)->before(':')->toString())->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        return collect($cart)->map(function ($item, $cartKey) use ($products) {
            $productId = (int) str($cartKey)->before(':')->toString();
            $product = $products->get((int) $productId);

            if (! $product) {
                return null;
            }

            $quantity = min((int) $item['quantity'], max(1, $product->stock));
            $unitPrice = isset($item['unit_price']) ? (float) $item['unit_price'] : (float) $product->price;

            return [
                'key' => $cartKey,
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'festival_id' => $item['festival_id'] ?? null,
                'festival_title' => $item['festival_title'] ?? null,
                'total' => $unitPrice * $quantity,
            ];
        })->filter()->values()->all();
    }

    public function snapshot(): array
    {
        $items = collect($this->items())->map(fn ($item) => [
            'id' => $item['product']->id,
            'key' => $item['key'],
            'name' => $item['product']->name,
            'image' => $item['product']->image_url,
            'price' => $item['unit_price'],
            'regular_price' => (float) $item['product']->price,
            'festival_title' => $item['festival_title'],
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

    private function festivalFor(Request $request, Product $product): ?Festival
    {
        if (! $request->filled('festival_id')) {
            return null;
        }

        $festival = Festival::find($request->integer('festival_id'));

        if (! $festival || ! $festival->isRunning()) {
            return null;
        }

        return $festival->includesProduct($product) ? $festival : null;
    }

    private function cartKey(int $productId, int $festivalId): string
    {
        return $productId.':'.$festivalId;
    }

    private function quantityForProduct(array $cart, int $productId, string $exceptKey): int
    {
        return collect($cart)
            ->except($exceptKey)
            ->sum(function ($item, $key) use ($productId) {
                return (int) str($key)->before(':')->toString() === $productId ? (int) ($item['quantity'] ?? 0) : 0;
            });
    }
}
