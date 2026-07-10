<x-user-dashboard-layout>
    <section>
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-wide text-primary">Order Details</p>
                <h1 class="text-4xl font-black text-ink">{{ $order->order_number }}</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('orders.receipt', $order) }}" class="rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-ink hover:border-primary hover:text-primary">Receipt</a>
                <span class="rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold capitalize">{{ str($order->status)->replace('_', ' ') }}</span>
            </div>
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
                @if($order->advance_delivery_required)
                    <div class="mt-5 rounded border border-accent/40 bg-accent/10 p-4 text-sm">
                        <p class="font-bold text-ink">Advance Delivery Charge</p>
                        <p class="mt-1">Area: {{ $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Inside Dhaka' }}</p>
                        <p>Charge: BDT {{ number_format($order->shipping, 2) }}</p>
                        <p>Option: {{ $order->delivery_charge_payment_option === 'pay_later' ? 'Pay Later' : 'Paid Now' }}</p>
                        @if($order->delivery_charge_payment_option === 'pay_now')
                            <p>Method: {{ $order->delivery_payment_method }}</p>
                            <p>Payment Mobile: {{ $order->delivery_payment_mobile }}</p>
                            <p>Transaction ID: {{ $order->delivery_transaction_id }}</p>
                        @else
                            <p class="mt-2 rounded bg-red-50 p-2 font-semibold text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত আপনার অর্ডার সম্পূর্ণভাবে কনফার্ম হবে না।</p>
                        @endif
                    </div>
                @endif
                <div class="mt-5 border-t pt-5 text-lg font-black text-ink">Total: BDT {{ number_format($order->total, 2) }}</div>
            </aside>
        </div>
    </section>
</x-user-dashboard-layout>
