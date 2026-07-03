<x-user-dashboard-layout>
    <section>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-primary">Order Details</p>
                <h1 class="text-4xl font-black text-ink">{{ $order->order_number }}</h1>
            </div>
            <span class="rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold capitalize">{{ $order->status }}</span>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-5 text-xl font-black text-ink">Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex justify-between border-b pb-4">
                            <div>
                                <p class="font-semibold text-ink">{{ $item->product_name }}</p>
                                <p class="text-sm text-gray-500">BDT {{ number_format($item->unit_price, 2) }} x {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold">BDT {{ number_format($item->total, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="h-fit rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="mb-4 text-xl font-black text-ink">Delivery</h2>
                <p class="font-semibold">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->mobile }}</p>
                <p class="mt-3 text-sm text-gray-600">{{ $order->address }}, {{ $order->city }}</p>
                <div class="mt-5 border-t pt-5 text-lg font-black text-ink">Total: BDT {{ number_format($order->total, 2) }}</div>
            </aside>
        </div>
    </section>
</x-user-dashboard-layout>
