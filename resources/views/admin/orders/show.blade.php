<x-admin-layout>
    <div class="mb-6 flex flex-col items-start justify-between gap-4 xl:flex-row xl:gap-6">
        <div><p class="text-sm font-bold uppercase tracking-wide text-primary">Order</p><h1 class="text-4xl font-black text-ink">{{ $order->order_number }}</h1></div>
        <div class="flex flex-wrap items-center gap-2">
            @if(in_array($order->status, ['confirmed', 'processing', 'delivered'], true))
                <a href="{{ route('admin.orders.receipt', $order) }}" target="_blank" class="inline-flex items-center gap-2 rounded bg-ink px-4 py-2 font-semibold text-white hover:bg-primary">
                    <i class="fa-solid fa-print"></i>
                    Print / Download Receipt
                </a>
            @endif
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
                        @if($order->delivery_payment_proof)
                            <a href="{{ route('admin.orders.payment-proof', $order) }}" target="_blank" class="mt-3 block overflow-hidden rounded-md border border-primary/20 bg-white p-2 hover:border-primary">
                                <img src="{{ route('admin.orders.payment-proof', $order) }}" alt="Delivery payment proof for {{ $order->order_number }}" class="max-h-56 w-full rounded object-contain">
                                <span class="mt-2 flex items-center justify-center gap-2 text-xs font-bold text-primary">
                                    <i class="fa-solid fa-up-right-from-square"></i>
                                    Open payment proof
                                </span>
                            </a>
                        @else
                            <p class="mt-2 text-xs font-semibold text-gray-500">No payment screenshot submitted.</p>
                        @endif
                    @else
                        <p class="mt-2 rounded bg-red-50 p-2 font-semibold text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত অর্ডার সম্পূর্ণভাবে কনফার্ম নয়।</p>
                    @endif
                </div>
            @endif
            <div class="mt-5 space-y-2 border-t pt-5 text-sm">
                <div class="flex justify-between gap-4"><span class="text-gray-500">Order total</span><span class="font-bold text-ink">BDT {{ number_format($order->total, 2) }}</span></div>
                <div class="flex justify-between gap-4"><span class="text-gray-500">Paid amount</span><span class="font-bold text-green-700">BDT {{ number_format($order->paidAmount(), 2) }}</span></div>
                <div class="flex justify-between gap-4 border-t pt-2 text-lg font-black"><span class="text-ink">Due amount</span><span class="text-red-700">BDT {{ number_format($order->dueAmount(), 2) }}</span></div>
            </div>
        </aside>
    </div>
</x-admin-layout>
