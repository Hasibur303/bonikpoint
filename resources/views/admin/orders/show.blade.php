<x-admin-layout>
    <div class="mb-6 flex items-start justify-between gap-6">
        <div><p class="text-sm font-bold uppercase tracking-wide text-primary">Order</p><h1 class="text-4xl font-black text-ink">{{ $order->order_number }}</h1></div>
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex gap-2">
            @csrf @method('PATCH')
            <select name="status" class="rounded border-gray-200">
                @foreach(['pending', 'processing', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
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
                        <tr><td class="p-3">{{ $item->product_name }}</td><td class="p-3">{{ $item->quantity }}</td><td class="p-3">৳{{ number_format($item->unit_price, 2) }}</td><td class="p-3 text-right">৳{{ number_format($item->total, 2) }}</td></tr>
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
            <div class="mt-5 border-t pt-5 text-lg font-black text-ink">Total: ৳{{ number_format($order->total, 2) }}</div>
        </aside>
    </div>
</x-admin-layout>
