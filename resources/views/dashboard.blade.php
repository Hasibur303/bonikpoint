<x-user-dashboard-layout>
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">My Account</p>
        <h1 class="text-4xl font-black text-ink">Dashboard</h1>
    </div>

    <div class="grid gap-5 md:grid-cols-3">
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total Orders</p>
            <p class="mt-2 text-3xl font-black text-ink">{{ $ordersCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Active Orders</p>
            <p class="mt-2 text-3xl font-black text-ink">{{ $pendingOrdersCount }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Cart Items</p>
            <p class="mt-2 text-3xl font-black text-ink">{{ \App\Http\Controllers\CartController::count() }}</p>
        </div>
    </div>

    <div class="mt-8 rounded-lg bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-black text-ink">Recent Orders</h2>
            <a href="{{ route('orders.index') }}" class="font-semibold text-primary">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr><th class="p-3">Order</th><th class="p-3">Total</th><th class="p-3">Status</th><th class="p-3">Date</th><th class="p-3"></th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($latestOrders as $order)
                        <tr>
                            <td class="p-3 font-semibold text-ink">{{ $order->order_number }}</td>
                            <td class="p-3">BDT {{ number_format($order->total, 2) }}</td>
                            <td class="p-3 capitalize">{{ $order->status }}</td>
                            <td class="p-3">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="p-3 text-right"><a href="{{ route('orders.show', $order) }}" class="font-semibold text-primary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-user-dashboard-layout>
