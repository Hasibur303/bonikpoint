<x-user-dashboard-layout>
    <section>
        <div class="mb-6 sm:mb-8">
            <p class="text-xs font-black uppercase tracking-wide text-primary">Account overview</p>
            <h1 class="mt-1 text-2xl font-black text-ink sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="mt-2 text-sm text-gray-500">Track orders, manage your profile, and continue shopping.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400">Total Orders</p>
                        <p class="mt-2 text-3xl font-black text-ink">{{ $ordersCount }}</p>
                    </div>
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-[#eaf5f3] text-primary"><i class="fa-solid fa-box-archive"></i></span>
                </div>
            </div>
            <div class="rounded-lg border border-[#ead9ad] bg-[#fff9e9] p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-[#806523]">Active Orders</p>
                        <p class="mt-2 text-3xl font-black text-ink">{{ $pendingOrdersCount }}</p>
                    </div>
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-[#f4e6ba] text-[#806523]"><i class="fa-solid fa-truck-fast"></i></span>
                </div>
            </div>
            <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase text-gray-400">Cart Items</p>
                        <p class="mt-2 text-3xl font-black text-ink">{{ \App\Http\Controllers\CartController::count() }}</p>
                    </div>
                    <span class="grid h-11 w-11 place-items-center rounded-md bg-[#eef4dc] text-[#65760f]"><i class="fa-solid fa-bag-shopping"></i></span>
                </div>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm sm:mt-8">
            <div class="flex items-center justify-between gap-4 border-b border-gray-100 px-5 py-4 sm:px-6">
                <div>
                    <p class="text-xs font-bold uppercase text-gray-400">Order activity</p>
                    <h2 class="mt-0.5 text-lg font-black text-ink">Recent Orders</h2>
                </div>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-ink">View all <i class="fa-solid fa-arrow-right text-xs"></i></a>
            </div>

            <div class="divide-y divide-gray-100 sm:hidden">
                @forelse($latestOrders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="block p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-all text-sm font-black text-ink">{{ $order->order_number }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                            </div>
                            <span class="rounded bg-[#eaf5f3] px-2 py-1 text-[10px] font-black capitalize text-primary">{{ str($order->status)->replace('_', ' ') }}</span>
                        </div>
                        <p class="mt-3 font-black text-ink">BDT {{ number_format($order->total, 2) }}</p>
                    </a>
                @empty
                    <div class="p-8 text-center text-sm text-gray-500">No orders yet.</div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto sm:block">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#f8f9fa] text-[10px] font-black uppercase text-gray-400">
                        <tr><th class="px-6 py-3">Order</th><th class="px-6 py-3">Total</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Date</th><th class="px-6 py-3"></th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($latestOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-ink">{{ $order->order_number }}</td>
                                <td class="px-6 py-4 font-semibold">BDT {{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4"><span class="rounded bg-[#eaf5f3] px-2 py-1 text-xs font-bold capitalize text-primary">{{ str($order->status)->replace('_', ' ') }}</span></td>
                                <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-right"><a href="{{ route('orders.show', $order) }}" class="font-bold text-primary hover:text-ink">View</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-8 text-center text-gray-500">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-user-dashboard-layout>
