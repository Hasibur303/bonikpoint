<x-admin-layout>
    <div class="mb-8">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">Admin Panel</p>
        <h1 class="text-4xl font-black text-ink">Dashboard</h1>
    </div>
    <div class="grid gap-5 md:grid-cols-4">
        <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Orders</p><p class="mt-2 text-3xl font-black text-ink">{{ $ordersCount }}</p></div>
        <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Products</p><p class="mt-2 text-3xl font-black text-ink">{{ $productsCount }}</p></div>
        <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Categories</p><p class="mt-2 text-3xl font-black text-ink">{{ $categoriesCount }}</p></div>
        <div class="rounded-lg bg-white p-5 shadow-sm"><p class="text-sm text-gray-500">Customers</p><p class="mt-2 text-3xl font-black text-ink">{{ $usersCount }}</p></div>
    </div>
    <div class="mt-5 rounded-lg bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-gray-500">Profit Tracking</p>
                <p class="mt-1 text-lg font-bold text-ink">View profit by selected date, full month, full year, or all time.</p>
            </div>
            <a href="{{ route('admin.profit.index') }}" class="rounded bg-primary px-5 py-2 font-semibold text-white">Open Profit Report</a>
        </div>
    </div>
    <div class="mt-8 rounded-lg bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-xl font-black text-ink">Recent Orders</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-3">Order</th><th class="p-3">Customer</th><th class="p-3">Total</th><th class="p-3">Status</th><th class="p-3"></th></tr></thead>
                <tbody class="divide-y">
                    @forelse($recentOrders as $order)
                        <tr><td class="p-3 font-semibold">{{ $order->order_number }}</td><td class="p-3">{{ $order->customer_name }}</td><td class="p-3">৳{{ number_format($order->total, 2) }}</td><td class="p-3 capitalize">{{ $order->status }}</td><td class="p-3 text-right"><a class="font-semibold text-primary" href="{{ route('admin.orders.show', $order) }}">View</a></td></tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-gray-500">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
