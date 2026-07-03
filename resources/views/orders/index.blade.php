<x-user-dashboard-layout>
    <section>
        <h1 class="mb-8 text-4xl font-black text-ink">My Orders</h1>

        <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-100">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="p-4">Order</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Date</th>
                        <th class="p-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                        <tr>
                            <td class="p-4 font-semibold text-ink">{{ $order->order_number }}</td>
                            <td class="p-4">BDT {{ number_format($order->total, 2) }}</td>
                            <td class="p-4 capitalize">{{ $order->status }}</td>
                            <td class="p-4">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="p-4 text-right">
                                <a href="{{ route('orders.show', $order) }}" class="font-semibold text-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-8 text-center text-gray-500">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </section>
</x-user-dashboard-layout>
