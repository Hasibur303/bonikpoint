<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OfflineSaleController extends Controller
{
    public function create(): View
    {
        return view('admin.offline-sales.create', [
            'products' => Product::with('category')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);

            if ($product->stock < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$product->stock} unit(s) of {$product->name} are currently in stock.",
                ]);
            }

            $unitPrice = round((float) $data['selling_price'], 2);
            $total = round($unitPrice * $data['quantity'], 2);

            $order = Order::create([
                'order_number' => 'OFF-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
                'customer_name' => 'Offline store sale',
                'email' => 'offline-sale@bonikpoint.local',
                'mobile' => 'N/A',
                'address' => 'Offline sale entered by admin',
                'city' => 'Store',
                'status' => 'delivered',
                'subtotal' => $total,
                'shipping' => 0,
                'total' => $total,
                'advance_delivery_required' => false,
                'is_offline_sale' => true,
                'notes' => filled($data['notes'] ?? null) ? trim($data['notes']) : null,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'buying_price' => $product->buying_price,
                'unit_price' => $unitPrice,
                'quantity' => $data['quantity'],
                'total' => $total,
            ]);

            $product->decrement('stock', $data['quantity']);

            return $order;
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Offline sale saved. Stock and profit report have been updated.');
    }
}
