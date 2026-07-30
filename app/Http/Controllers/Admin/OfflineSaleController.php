<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Rules\BangladeshMobile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
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
        $requiresCourier = $request->boolean('requires_courier');

        if (filled($request->input('mobile'))) {
            $request->merge([
                'mobile' => BangladeshMobile::normalize($request->input('mobile')) ?? $request->input('mobile'),
            ]);
        }

        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'selling_price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'requires_courier' => ['nullable', 'boolean'],
            'offline_payment_status' => [Rule::requiredIf($requiresCourier), 'nullable', Rule::in(['paid', 'cod'])],
            'delivery_charge' => [Rule::requiredIf($requiresCourier), 'nullable', 'numeric', 'min:0', 'max:999999.99'],
            'customer_name' => [Rule::requiredIf($requiresCourier), 'nullable', 'string', 'max:120'],
            'mobile' => [Rule::requiredIf($requiresCourier), 'nullable', 'string', 'max:30', new BangladeshMobile],
            'city' => [Rule::requiredIf($requiresCourier), 'nullable', 'string', 'max:100'],
            'thana' => [Rule::requiredIf($requiresCourier), 'nullable', 'string', 'max:120'],
            'address' => [Rule::requiredIf($requiresCourier), 'nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = DB::transaction(function () use ($data, $requiresCourier) {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);

            if ($product->stock < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$product->stock} unit(s) of {$product->name} are currently in stock.",
                ]);
            }

            $unitPrice = round((float) $data['selling_price'], 2);
            $subtotal = round($unitPrice * $data['quantity'], 2);
            $shipping = $requiresCourier ? round((float) $data['delivery_charge'], 2) : 0;
            $total = $subtotal + $shipping;

            $order = Order::create([
                'order_number' => 'OFF-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
                'customer_name' => filled($data['customer_name'] ?? null) ? trim($data['customer_name']) : 'Offline store sale',
                'email' => 'offline-sale@bonikpoint.local',
                'mobile' => filled($data['mobile'] ?? null) ? trim($data['mobile']) : 'N/A',
                'address' => filled($data['address'] ?? null) ? trim($data['address']) : 'Offline sale entered by admin',
                'city' => filled($data['city'] ?? null) ? trim($data['city']) : 'Store',
                'thana' => filled($data['thana'] ?? null) ? trim($data['thana']) : null,
                'status' => 'confirmed',
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $total,
                'advance_delivery_required' => false,
                'is_offline_sale' => true,
                'requires_courier' => $requiresCourier,
                'offline_payment_collected' => ! $requiresCourier || $data['offline_payment_status'] === 'paid',
                'notes' => filled($data['notes'] ?? null) ? trim($data['notes']) : null,
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'buying_price' => $product->buying_price,
                'unit_price' => $unitPrice,
                'quantity' => $data['quantity'],
                'total' => $subtotal,
            ]);

            $product->decrement('stock', $data['quantity']);

            return $order;
        });

        return redirect()->route('admin.orders.show', $order)
            ->with('success', $requiresCourier
                ? 'Offline order saved. Review the details, then send the parcel to Steadfast.'
                : 'Offline sale saved. Stock and profit report have been updated.');
    }
}
