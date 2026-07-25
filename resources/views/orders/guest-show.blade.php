<x-app-layout>
    <section class="bg-[#f4f7f6] py-6 md:py-14">
        <div class="container">
            <div class="mb-6 flex flex-col items-stretch gap-4 sm:flex-row sm:items-start sm:justify-between md:mb-8">
                <div class="min-w-0">
                    <p class="text-sm font-bold uppercase tracking-wide text-primary">Guest Order</p>
                    <h1 class="mt-2 break-words text-2xl font-black text-ink md:text-4xl">{{ $order->order_number }}</h1>
                    <p class="mt-2 text-sm text-gray-500">Please keep this page link or receipt for future order reference.</p>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-3">
                    <a href="{{ route('guest.orders.receipt', [$order->order_number, $order->guest_token]) }}" class="inline-flex min-w-0 items-center justify-center rounded-full border border-gray-200 bg-white px-3 py-2 text-center text-xs font-semibold text-ink hover:border-primary hover:text-primary sm:px-4 sm:text-sm">Receipt</a>
                    <span class="inline-flex min-w-0 items-center justify-center rounded-full bg-white px-3 py-2 text-center text-xs font-semibold capitalize text-ink shadow-sm sm:px-4 sm:text-sm">{{ str($order->status)->replace('_', ' ') }}</span>
                </div>
            </div>

            <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
                <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100 sm:p-6">
                    <h2 class="mb-5 text-xl font-black text-ink">Items</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex flex-col gap-2 border-b pb-4 last:border-b-0 last:pb-0 sm:flex-row sm:justify-between sm:gap-4">
                                <div class="min-w-0">
                                    <p class="font-semibold text-ink">{{ $item->product_name }}</p>
                                    @if($item->selected_color_name)
                                        <p class="mt-1 flex items-center gap-2 text-xs font-semibold text-gray-500">
                                            <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: {{ $item->selected_color_hex ?: '#E5E7EB' }}"></span>
                                            Color: {{ $item->selected_color_name }}
                                        </p>
                                    @endif
                                    @if($item->selected_flavor_name)
                                        <span class="mt-1 block text-xs font-semibold text-gray-500">Flavor: {{ $item->selected_flavor_name }}</span>
                                    @endif
                                    <p class="mt-1 text-sm text-gray-500">BDT {{ number_format($item->unit_price, 2) }} x {{ $item->quantity }}</p>
                                </div>
                                <p class="shrink-0 font-semibold text-ink sm:text-right">BDT {{ number_format($item->total, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <aside class="h-fit rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100 sm:p-6">
                    <h2 class="mb-4 text-xl font-black text-ink">Delivery</h2>
                    <p class="font-semibold">{{ $order->customer_name }}</p>
                    <p class="text-sm text-gray-600">{{ $order->mobile }}</p>
                    <p class="text-sm text-gray-600">{{ $order->email }}</p>
                    <p class="mt-3 text-sm text-gray-600">{{ $order->address }}, {{ $order->city }}</p>
                    @if($order->advance_delivery_required)
                        <div class="mt-5 rounded border border-accent/40 bg-accent/10 p-3 text-sm sm:p-4">
                            <p class="font-bold text-ink">Advance Delivery Charge</p>
                            <p class="mt-1">Area: {{ $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Inside Dhaka' }}</p>
                            <p>Charge: BDT {{ number_format($order->shipping, 2) }}</p>
                            <p>Method: {{ $order->delivery_payment_method }}</p>
                            <p>Payment Mobile: {{ $order->delivery_payment_mobile }}</p>
                            <p>Transaction ID: {{ $order->delivery_transaction_id }}</p>
                            @if($order->delivery_payment_proof)
                                <p class="mt-2 inline-flex items-center gap-2 rounded bg-green-50 px-2 py-1 font-semibold text-green-700"><i class="fa-solid fa-circle-check"></i> Payment screenshot submitted</p>
                            @endif
                        </div>
                    @endif
                    <div class="mt-5 space-y-2 border-t pt-5 text-sm">
                        <div class="flex justify-between gap-4"><span>Order total</span><span class="font-bold">BDT {{ number_format($order->total, 2) }}</span></div>
                        <div class="flex justify-between gap-4 text-green-700"><span>Paid amount</span><span class="font-bold">BDT {{ number_format($order->paidAmount(), 2) }}</span></div>
                        <div class="flex justify-between gap-4 text-lg font-black text-ink"><span>Due amount</span><span>BDT {{ number_format($order->dueAmount(), 2) }}</span></div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-app-layout>
