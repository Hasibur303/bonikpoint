<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Finance</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Profit Report</h1>
            <p class="mt-2 text-sm text-gray-500">Delivered online sales and recorded offline sales for <span class="font-bold text-ink">{{ $periodLabel }}</span>@if($selectedCategoryName) in <span class="font-bold text-ink">{{ $selectedCategoryName }}</span>@endif.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.offline-sales.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-primary px-4 text-xs font-black text-white shadow-sm hover:bg-ink"><i class="fa-solid fa-cash-register"></i>Add Offline Sale</a>
            <a href="{{ route('admin.products.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-[#d7e1df] bg-white px-4 text-xs font-black text-ink shadow-sm hover:border-primary hover:text-primary"><i class="fa-solid fa-pen"></i>Update Buying Prices</a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.profit.index') }}" class="mb-5 rounded-lg border border-[#dfe7e5] bg-white p-4 shadow-sm">
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[160px_1fr_1fr_1fr_1fr_auto]">
            <div><label class="mb-1.5 block text-xs font-black">Report Type</label><select name="filter" class="h-11 w-full text-sm"><option value="day" @selected($filter === 'day')>Selected Date</option><option value="month" @selected($filter === 'month')>Full Month</option><option value="year" @selected($filter === 'year')>Full Year</option><option value="all" @selected($filter === 'all')>All Time</option></select></div>
            <div><label class="mb-1.5 block text-xs font-black">Category</label><select name="category" class="h-11 w-full text-sm"><option value="">All Categories</option>@foreach($categories->whereNull('parent_id') as $category)<option value="{{ $category->id }}" @selected($selectedCategoryId === $category->id)>{{ $category->name }} (all)</option>@foreach($category->children as $child)<option value="{{ $child->id }}" @selected($selectedCategoryId === $child->id)>-- {{ $child->name }}</option>@endforeach @endforeach</select></div>
            <div><label class="mb-1.5 block text-xs font-black">Date</label><input type="date" name="date" value="{{ $date }}" class="h-11 w-full text-sm"></div>
            <div><label class="mb-1.5 block text-xs font-black">Month</label><input type="month" name="month" value="{{ $month }}" class="h-11 w-full text-sm"></div>
            <div><label class="mb-1.5 block text-xs font-black">Year</label><input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="h-11 w-full text-sm"></div>
            <div class="flex items-end"><button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-md bg-ink px-5 text-xs font-black text-white hover:bg-primary"><i class="fa-solid fa-chart-simple"></i>Generate</button></div>
        </div>
    </form>

    @php
        $profitMetrics = [
            ['label' => 'Revenue', 'value' => 'BDT '.number_format((float) $summary->revenue, 2), 'icon' => 'fa-arrow-trend-up', 'class' => 'bg-blue-50 text-blue-700'],
            ['label' => 'Buying Cost', 'value' => 'BDT '.number_format((float) $summary->cost, 2), 'icon' => 'fa-boxes-stacked', 'class' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Gross Profit', 'value' => 'BDT '.number_format((float) $summary->profit, 2), 'icon' => 'fa-sack-dollar', 'class' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Orders', 'value' => number_format($summary->orders_count), 'icon' => 'fa-receipt', 'class' => 'bg-[#e5f4f2] text-primary'],
            ['label' => 'Units Sold', 'value' => number_format($summary->units), 'icon' => 'fa-cubes', 'class' => 'bg-[#f0f5d7] text-[#617311]'],
        ];
    @endphp
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach($profitMetrics as $metric)
            <article class="rounded-lg border border-[#dfe7e5] bg-white p-4 shadow-sm"><div class="flex items-start justify-between gap-3"><div><p class="text-xs font-bold text-gray-500">{{ $metric['label'] }}</p><p class="mt-2 text-xl font-black text-ink">{{ $metric['value'] }}</p></div><span class="grid h-9 w-9 shrink-0 place-items-center rounded-md {{ $metric['class'] }}"><i class="fa-solid {{ $metric['icon'] }} text-xs"></i></span></div></article>
        @endforeach
    </div>

    <section class="mt-6 overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="border-b border-[#e7edeb] px-5 py-4"><h2 class="text-base font-black text-ink">Product Performance</h2><p class="mt-0.5 text-xs text-gray-500">Revenue, cost, and gross profit by product{{ $selectedCategoryName ? ' in '.$selectedCategoryName : '' }}.</p></div>
        <div class="overflow-x-auto"><table class="min-w-[720px] w-full text-left text-sm"><thead><tr><th class="px-5 py-3">Product</th><th class="px-4 py-3">Quantity</th><th class="px-4 py-3">Revenue</th><th class="px-4 py-3">Buying Cost</th><th class="px-5 py-3 text-right">Profit</th></tr></thead><tbody>
            @forelse($products as $product)
                <tr><td class="px-5 py-4 font-black text-ink">{{ $product->product_name }}</td><td class="px-4 py-4 font-bold text-gray-600">{{ $product->quantity_sold }}</td><td class="px-4 py-4 font-bold text-ink">BDT {{ number_format((float) $product->revenue, 2) }}</td><td class="px-4 py-4 text-gray-600">BDT {{ number_format((float) $product->cost, 2) }}</td><td class="px-5 py-4 text-right font-black text-emerald-700">BDT {{ number_format((float) $product->profit, 2) }}</td></tr>
            @empty
                <tr><td colspan="5" class="px-5 py-14 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-gray-100 text-gray-400"><i class="fa-solid fa-chart-column"></i></span><p class="mt-3 font-bold text-ink">No sales for this period</p><p class="mt-1 text-xs text-gray-500">Choose another date range to review performance.</p></td></tr>
            @endforelse
        </tbody></table></div>
    </section>
    <p class="mt-4 flex items-start gap-2 text-xs text-gray-500"><i class="fa-solid fa-circle-info mt-0.5 text-primary"></i><span>Profit includes delivered online sales and all non-cancelled offline sales. Order-level discounts and extra charges are allocated proportionally across the order's products. Accuracy depends on each product's buying price.</span></p>
</x-admin-layout>
