<x-admin-layout>
    <div class="mb-6"><p class="text-sm font-bold uppercase tracking-wide text-primary">Sales</p><h1 class="text-4xl font-black text-ink">Orders</h1></div>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-4">Order</th><th class="p-4">Customer</th><th class="p-4">Total</th><th class="p-4">Status</th><th class="p-4">Date</th><th class="p-4"></th></tr></thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                    <tr>
                        <td class="p-4 font-semibold text-ink">{{ $order->order_number }}</td>
                        <td class="p-4">{{ $order->customer_name }}</td>
                        <td class="p-4">৳{{ number_format($order->total, 2) }}</td>
                        <td class="p-4 capitalize">{{ str($order->status)->replace('_', ' ') }}</td>
                        <td class="p-4">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="p-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-primary">Manage</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $orders->links() }}</div>
</x-admin-layout>
