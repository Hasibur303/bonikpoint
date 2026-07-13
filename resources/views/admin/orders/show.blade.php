<x-admin-layout>
    <div class="mb-6 flex items-start justify-between gap-6">
        <div><p class="text-sm font-bold uppercase tracking-wide text-primary">Order</p><h1 class="text-4xl font-black text-ink">{{ $order->order_number }}</h1></div>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-2">
            @csrf @method('PATCH')
            <select name="status" class="rounded border-gray-200">
                @foreach(['waiting_delivery_charge', 'pending', 'confirmed', 'processing', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected($order->status === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                @endforeach
            </select>
            <button class="rounded bg-primary px-4 py-2 font-semibold text-white">Update</button>
        </form>
    </div>
    <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-black text-ink">Items</h2>
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-3">Product</th><th class="p-3">Qty</th><th class="p-3">Price</th><th class="p-3 text-right">Total</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="p-3">
                                {{ $item->product_name }}
                                @if($item->selected_color_name)
                                    <span class="mt-1 flex items-center gap-2 text-xs font-semibold text-gray-500">
                                        <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: {{ $item->selected_color_hex ?: '#E5E7EB' }}"></span>
                                        Color: {{ $item->selected_color_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3">{{ $item->quantity }}</td>
                            <td class="p-3">৳{{ number_format($item->unit_price, 2) }}</td>
                            <td class="p-3 text-right">৳{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <aside class="h-fit rounded-lg bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-xl font-black text-ink">Customer</h2>
            <p class="font-semibold">{{ $order->customer_name }}</p>
            <p class="text-sm text-gray-600">{{ $order->email }}</p>
            <p class="text-sm text-gray-600">{{ $order->mobile }}</p>
            <p class="mt-3 text-sm text-gray-600">{{ $order->address }}, {{ $order->city }}</p>
            @if($order->notes)<p class="mt-3 rounded bg-gray-50 p-3 text-sm">{{ $order->notes }}</p>@endif
            @if($order->advance_delivery_required)
                <div class="mt-5 rounded border border-accent/40 bg-accent/10 p-4 text-sm">
                    <p class="font-bold text-ink">Advance Delivery Charge</p>
                    <p class="mt-1">Area: {{ $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Inside Dhaka' }}</p>
                    <p>Charge: BDT {{ number_format($order->shipping, 2) }}</p>
                    <p>Option: {{ $order->delivery_charge_payment_option === 'pay_later' ? 'Pay Later' : 'Paid Now' }}</p>
                    @if($order->delivery_charge_payment_option === 'pay_now')
                        <p>Method: {{ $order->delivery_payment_method }}</p>
                        <p>Payment Mobile: {{ $order->delivery_payment_mobile }}</p>
                        <p>Transaction ID: {{ $order->delivery_transaction_id }}</p>
                    @else
                        <p class="mt-2 rounded bg-red-50 p-2 font-semibold text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত অর্ডার সম্পূর্ণভাবে কনফার্ম নয়।</p>
                    @endif
                </div>
            @endif
            <div class="mt-5 border-t pt-5 text-lg font-black text-ink">Total: BDT {{ number_format($order->total, 2) }}</div>
        </aside>
    </div>
</x-admin-layout>
