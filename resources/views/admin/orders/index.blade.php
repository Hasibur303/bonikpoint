<x-admin-layout>
    <div class="mb-6">
        <p class="text-xs font-black uppercase tracking-wide text-primary">Sales</p>
        <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Orders</h1>
        <p class="mt-2 text-sm text-gray-500">Review payments, update fulfillment, and manage customer orders.</p>
    </div>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="mb-4 flex flex-col gap-2 rounded-lg border border-[#dfe7e5] bg-white p-3 shadow-sm sm:flex-row">
        @if($selectedStatus)
            <input type="hidden" name="status" value="{{ $selectedStatus }}">
        @endif
        <label class="relative min-w-0 flex-1">
            <span class="sr-only">Search orders by parcel ID</span>
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            <input name="search" value="{{ $search }}" placeholder="Search parcel ID, order number, customer, or mobile" class="h-11 w-full pl-10 text-sm">
        </label>
        <div class="flex gap-2">
            <button class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-md bg-ink px-5 text-xs font-black text-white transition hover:bg-primary sm:flex-none"><i class="fa-solid fa-magnifying-glass"></i>Search Orders</button>
            @if($search)
                <a href="{{ route('admin.orders.index', array_filter(['status' => $selectedStatus])) }}" class="grid h-11 w-11 shrink-0 place-items-center rounded-md border border-gray-200 text-gray-500 transition hover:border-red-200 hover:text-red-600" aria-label="Clear order search" title="Clear search"><i class="fa-solid fa-xmark"></i></a>
            @endif
        </div>
    </form>

    <div class="mb-5 overflow-x-auto rounded-lg border border-[#dfe7e5] bg-white p-2 shadow-sm">
        <div class="flex min-w-max gap-1.5">
            <a href="{{ route('admin.orders.index', array_filter(['search' => $search])) }}" class="inline-flex h-10 items-center gap-2 rounded-md px-3.5 text-xs font-black transition {{ $selectedStatus ? 'text-gray-600 hover:bg-[#f2f6f5] hover:text-ink' : 'bg-ink text-white shadow-sm' }}">
                All Orders
                <span class="rounded-full px-2 py-0.5 text-[10px] {{ $selectedStatus ? 'bg-gray-100 text-gray-500' : 'bg-white/15 text-white' }}">{{ $allOrdersCount }}</span>
            </a>
            @foreach($statuses as $status)
                <a href="{{ route('admin.orders.index', array_filter(['status' => $status, 'search' => $search])) }}" class="inline-flex h-10 items-center gap-2 rounded-md px-3.5 text-xs font-black capitalize transition {{ $selectedStatus === $status ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-[#f2f6f5] hover:text-ink' }}">
                    {{ str($status)->replace('_', ' ') }}
                    <span class="rounded-full px-2 py-0.5 text-[10px] {{ $selectedStatus === $status ? 'bg-white/15 text-white' : 'bg-gray-100 text-gray-500' }}">{{ $statusCounts[$status] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#e7edeb] px-5 py-3.5">
            <p class="text-xs font-bold text-gray-500">{{ $orders->total() }} {{ str('order')->plural($orders->total()) }}</p>
            @if($selectedStatus || $search)
                <a href="{{ route('admin.orders.index') }}" class="text-[11px] font-black text-primary hover:text-ink">Clear all filters</a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1050px] w-full text-left text-sm">
                <thead><tr><th class="px-5 py-3">Order</th><th class="px-4 py-3">Parcel ID</th><th class="px-4 py-3">Customer</th><th class="px-4 py-3">Payment</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Placed</th><th class="px-5 py-3 text-right">Action</th></tr></thead>
                <tbody>
                    @forelse($orders as $order)
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
                            $paymentPending = $order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later' && ! in_array($order->status, ['delivered', 'cancelled'], true);
                        @endphp
                        <tr>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-black text-ink hover:text-primary">{{ $order->order_number }}</a>
                                <span class="mt-0.5 block text-[11px] text-gray-400">{{ $order->user_id ? 'Customer account' : 'Guest order' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                @if($order->parcel_id)
                                    <span class="inline-flex rounded-md bg-[#edf5f3] px-2.5 py-1 font-mono text-[11px] font-black text-primary">{{ $order->parcel_id }}</span>
                                @else
                                    <span class="text-xs font-semibold text-gray-400">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-4"><span class="block font-bold text-gray-700">{{ $order->customer_name }}</span><span class="mt-0.5 block text-[11px] text-gray-400">{{ $order->mobile }}</span></td>
                            <td class="px-4 py-4">
                                @if($paymentPending)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-black text-amber-800 ring-1 ring-amber-200"><i class="fa-solid fa-circle-exclamation"></i>Charge pending</span>
                                @elseif($order->delivery_charge_payment_option === 'pay_now')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black text-emerald-700 ring-1 ring-emerald-200"><i class="fa-solid fa-check"></i>Submitted</span>
                                @else
                                    <span class="text-xs font-semibold text-gray-400">Not required</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 font-black text-ink">BDT {{ number_format($order->total, 2) }}</td>
                            <td class="px-4 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black capitalize ring-1 {{ $statusClass }}">{{ str($order->status)->replace('_', ' ') }}</span></td>
                            <td class="px-4 py-4"><span class="block text-xs font-bold text-gray-600">{{ $order->created_at->format('d M Y') }}</span><span class="text-[11px] text-gray-400">{{ $order->created_at->format('h:i A') }}</span></td>
                            <td class="px-5 py-4 text-right"><a href="{{ route('admin.orders.show', $order) }}" class="inline-flex h-9 items-center gap-2 rounded-md border border-gray-200 px-3 text-xs font-black text-ink transition hover:border-primary hover:text-primary">Manage<i class="fa-solid fa-arrow-right text-[10px]"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-14 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-gray-100 text-gray-400"><i class="fa-solid fa-receipt"></i></span><p class="mt-3 font-bold text-ink">No orders found</p><p class="mt-1 text-xs text-gray-500">Try a different parcel ID, order number, or status.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="mt-6">{{ $orders->links() }}</div>
</x-admin-layout>
