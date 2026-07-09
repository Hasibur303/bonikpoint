<x-admin-layout>
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-primary">Accounts</p>
            <h1 class="text-4xl font-black text-ink">Profit Report</h1>
            <p class="mt-2 text-sm text-gray-500">Showing non-cancelled orders for {{ $periodLabel }}.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="rounded bg-ink px-5 py-2 font-semibold text-white">Update Buying Prices</a>
    </div>

    <form method="GET" action="{{ route('admin.profit.index') }}" class="mb-6 rounded-lg bg-white p-5 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-[180px_1fr_1fr_1fr_auto]">
            <div>
                <label class="mb-1 block text-sm font-semibold">Report Type</label>
                <select name="filter" class="w-full rounded border-gray-200">
                    <option value="day" @selected($filter === 'day')>Selected Date</option>
                    <option value="month" @selected($filter === 'month')>Full Month</option>
                    <option value="year" @selected($filter === 'year')>Full Year</option>
                    <option value="all" @selected($filter === 'all')>All Time</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Date</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full rounded border-gray-200">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Month</label>
                <input type="month" name="month" value="{{ $month }}" class="w-full rounded border-gray-200">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Year</label>
                <input type="number" name="year" value="{{ $year }}" min="2000" max="2100" class="w-full rounded border-gray-200">
            </div>
            <div class="flex items-end">
                <button class="w-full rounded bg-primary px-6 py-2.5 font-semibold text-white">Show Profit</button>
            </div>
        </div>
    </form>

    <div class="grid gap-5 md:grid-cols-5">
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Revenue</p>
            <p class="mt-2 text-2xl font-black text-ink">BDT {{ number_format((float) $summary->revenue, 2) }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Buying Cost</p>
            <p class="mt-2 text-2xl font-black text-ink">BDT {{ number_format((float) $summary->cost, 2) }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Profit</p>
            <p class="mt-2 text-2xl font-black text-primary">BDT {{ number_format((float) $summary->profit, 2) }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Orders</p>
            <p class="mt-2 text-2xl font-black text-ink">{{ $summary->orders_count }}</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Units Sold</p>
            <p class="mt-2 text-2xl font-black text-ink">{{ $summary->units }}</p>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="p-4">Product</th>
                    <th class="p-4">Qty</th>
                    <th class="p-4">Revenue</th>
                    <th class="p-4">Buying Cost</th>
                    <th class="p-4">Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr>
                        <td class="p-4 font-semibold text-ink">{{ $product->product_name }}</td>
                        <td class="p-4">{{ $product->quantity_sold }}</td>
                        <td class="p-4">BDT {{ number_format((float) $product->revenue, 2) }}</td>
                        <td class="p-4">BDT {{ number_format((float) $product->cost, 2) }}</td>
                        <td class="p-4 font-bold text-primary">BDT {{ number_format((float) $product->profit, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">No sales found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-sm text-gray-500">Set each product's buying price for accurate profit. Existing orders use the saved order cost when available, otherwise the current product buying price is used.</p>
</x-admin-layout>
