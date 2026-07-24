<x-admin-layout>
    <div class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Overview</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Dashboard</h1>
            <p class="mt-2 text-sm text-gray-500">A quick view of your store activity and work that needs attention.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.orders.index') }}" class="inline-flex h-10 items-center gap-2 rounded-md border border-[#d7e1df] bg-white px-4 text-xs font-black text-ink shadow-sm transition hover:border-primary hover:text-primary">
                <i class="fa-solid fa-receipt"></i>
                Manage Orders
            </a>
            <a href="{{ route('admin.products.create') }}" class="inline-flex h-10 items-center gap-2 rounded-md bg-primary px-4 text-xs font-black text-white shadow-[0_8px_20px_rgba(8,124,127,0.16)] transition hover:bg-ink">
                <i class="fa-solid fa-plus"></i>
                Add Product
            </a>
        </div>
    </div>

    @php
        $metrics = [
            ['label' => 'Total Orders', 'value' => $ordersCount, 'icon' => 'fa-bag-shopping', 'iconClass' => 'bg-[#e5f4f2] text-primary', 'note' => $todayOrdersCount.' received today'],
            ['label' => 'Products', 'value' => $productsCount, 'icon' => 'fa-box-open', 'iconClass' => 'bg-blue-50 text-blue-700', 'note' => $lowStockCount.' low-stock items'],
            ['label' => 'Categories', 'value' => $categoriesCount, 'icon' => 'fa-layer-group', 'iconClass' => 'bg-amber-50 text-amber-700', 'note' => 'Main and subcategories'],
            ['label' => 'Customers', 'value' => $usersCount, 'icon' => 'fa-users', 'iconClass' => 'bg-[#f0f5d7] text-[#617311]', 'note' => 'Registered accounts'],
        ];
    @endphp

    <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        @foreach($metrics as $metric)
            <article class="rounded-lg border border-[#dfe7e5] bg-white p-4 shadow-[0_10px_30px_rgba(20,60,64,0.05)] sm:p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold text-gray-500">{{ $metric['label'] }}</p>
                        <p class="mt-2 text-2xl font-black text-ink sm:text-3xl">{{ number_format($metric['value']) }}</p>
                    </div>
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md sm:h-10 sm:w-10 {{ $metric['iconClass'] }}">
                        <i class="fa-solid {{ $metric['icon'] }} text-sm"></i>
                    </span>
                </div>
                <p class="mt-3 border-t border-gray-100 pt-3 text-[10px] font-semibold leading-4 text-gray-400 sm:mt-4 sm:text-xs">{{ $metric['note'] }}</p>
            </article>
        @endforeach
    </div>

    <section class="mt-5 overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-[0_10px_30px_rgba(20,60,64,0.05)]">
        <div class="flex flex-col gap-1 border-b border-[#e7edeb] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-base font-black text-ink">Visitor Activity</h2>
                <p class="mt-0.5 text-xs text-gray-500">Anonymous unique visitors. Tracking starts after this update is deployed.</p>
            </div>
            <i class="fa-solid fa-chart-simple text-primary"></i>
        </div>
        <div class="grid grid-cols-2 divide-x divide-y divide-[#e7edeb] sm:grid-cols-4 sm:divide-y-0">
            @foreach([
                ['label' => 'Today', 'value' => $visitorStats['today']],
                ['label' => 'This Week', 'value' => $visitorStats['week']],
                ['label' => 'This Month', 'value' => $visitorStats['month']],
                ['label' => 'All Time', 'value' => $visitorStats['total']],
            ] as $visitorMetric)
                <div class="p-4 sm:p-5">
                    <p class="text-xs font-bold text-gray-500">{{ $visitorMetric['label'] }}</p>
                    <p class="mt-2 text-2xl font-black text-ink sm:text-3xl">{{ number_format($visitorMetric['value']) }}</p>
                    <p class="mt-1 text-[10px] font-semibold uppercase tracking-wide text-primary">Unique visitors</p>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mt-5 grid overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-[0_10px_30px_rgba(20,60,64,0.05)] sm:grid-cols-3">
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="flex items-center gap-4 border-b border-[#e7edeb] p-5 transition hover:bg-blue-50/50 sm:border-b-0 sm:border-r">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-blue-50 text-blue-700"><i class="fa-solid fa-clock"></i></span>
            <span><span class="block text-2xl font-black text-ink">{{ $pendingOrdersCount }}</span><span class="text-xs font-bold text-gray-500">Orders need review</span></span>
            <i class="fa-solid fa-chevron-right ml-auto text-xs text-gray-300"></i>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'waiting_delivery_charge']) }}" class="flex items-center gap-4 border-b border-[#e7edeb] p-5 transition hover:bg-amber-50/50 sm:border-b-0 sm:border-r">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-amber-50 text-amber-700"><i class="fa-solid fa-wallet"></i></span>
            <span><span class="block text-2xl font-black text-ink">{{ $awaitingPaymentCount }}</span><span class="text-xs font-bold text-gray-500">Waiting for delivery charge</span></span>
            <i class="fa-solid fa-chevron-right ml-auto text-xs text-gray-300"></i>
        </a>
        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-4 p-5 transition hover:bg-red-50/40">
            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-red-50 text-red-700"><i class="fa-solid fa-triangle-exclamation"></i></span>
            <span><span class="block text-2xl font-black text-ink">{{ $lowStockCount }}</span><span class="text-xs font-bold text-gray-500">Low-stock products</span></span>
            <i class="fa-solid fa-chevron-right ml-auto text-xs text-gray-300"></i>
        </a>
    </section>

    <div class="mt-7 grid gap-6 xl:grid-cols-[minmax(0,1fr)_310px]">
        <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-[0_10px_30px_rgba(20,60,64,0.05)]">
            <div class="flex items-center justify-between gap-4 border-b border-[#e7edeb] px-5 py-4">
                <div>
                    <h2 class="text-base font-black text-ink">Recent Orders</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Latest customer purchases</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-black text-primary hover:text-ink">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-[700px] w-full text-left text-sm">
                    <thead><tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th><th class="px-5 py-3"></th></tr></thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            @php
                                $statusClass = match($order->status) {
                                    'waiting_delivery_charge' => 'bg-amber-50 text-amber-800 ring-amber-200',
                                    'pending' => 'bg-blue-50 text-blue-700 ring-blue-200',
                                    'confirmed' => 'bg-cyan-50 text-cyan-800 ring-cyan-200',
                                    'processing' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
                                    'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                    'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
                                    default => 'bg-gray-50 text-gray-700 ring-gray-200',
                                };
                            @endphp
                            <tr>
                                <td class="px-5 py-4 font-black text-ink">{{ $order->order_number }}</td>
                                <td class="px-5 py-4"><span class="block font-semibold text-gray-700">{{ $order->customer_name }}</span><span class="text-xs text-gray-400">{{ $order->created_at->format('d M, h:i A') }}</span></td>
                                <td class="px-5 py-4 font-bold text-ink">BDT {{ number_format($order->total, 2) }}</td>
                                <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black capitalize ring-1 {{ $statusClass }}">{{ str($order->status)->replace('_', ' ') }}</span></td>
                                <td class="px-5 py-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="inline-grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 transition hover:border-primary hover:text-primary" aria-label="Manage order {{ $order->order_number }}"><i class="fa-solid fa-arrow-right text-xs"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="h-fit rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-[0_10px_30px_rgba(20,60,64,0.05)]">
            <h2 class="text-base font-black text-ink">Quick Actions</h2>
            <p class="mt-1 text-xs text-gray-500">Common store management tasks</p>
            <div class="mt-4 grid gap-2">
                <a href="{{ route('admin.products.create') }}" class="flex h-11 items-center gap-3 rounded-md bg-[#f5f8f7] px-3 text-sm font-bold text-ink transition hover:bg-[#e8f2f0] hover:text-primary"><span class="grid h-7 w-7 place-items-center rounded-md bg-white text-primary shadow-sm"><i class="fa-solid fa-plus text-xs"></i></span>Add a product<i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i></a>
                <a href="{{ route('admin.festivals.create') }}" class="flex h-11 items-center gap-3 rounded-md bg-[#f5f8f7] px-3 text-sm font-bold text-ink transition hover:bg-[#e8f2f0] hover:text-primary"><span class="grid h-7 w-7 place-items-center rounded-md bg-white text-amber-600 shadow-sm"><i class="fa-solid fa-tags text-xs"></i></span>Create an offer<i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i></a>
                <a href="{{ route('admin.profit.index') }}" class="flex h-11 items-center gap-3 rounded-md bg-[#f5f8f7] px-3 text-sm font-bold text-ink transition hover:bg-[#e8f2f0] hover:text-primary"><span class="grid h-7 w-7 place-items-center rounded-md bg-white text-blue-600 shadow-sm"><i class="fa-solid fa-chart-line text-xs"></i></span>Profit report<i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i></a>
                <a href="{{ route('admin.settings.edit') }}" class="flex h-11 items-center gap-3 rounded-md bg-[#f5f8f7] px-3 text-sm font-bold text-ink transition hover:bg-[#e8f2f0] hover:text-primary"><span class="grid h-7 w-7 place-items-center rounded-md bg-white text-gray-600 shadow-sm"><i class="fa-solid fa-sliders text-xs"></i></span>Store settings<i class="fa-solid fa-chevron-right ml-auto text-[10px] text-gray-300"></i></a>
            </div>
        </aside>
    </div>
</x-admin-layout>
