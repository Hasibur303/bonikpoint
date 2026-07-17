<x-user-dashboard-layout>
    <section>
        @php
            $statusStyle = match($order->status) {
                'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                'waiting_delivery_charge' => 'bg-amber-50 text-amber-700 border-amber-200',
                default => 'bg-[#eaf5f3] text-primary border-[#c9dfdb]',
            };
        @endphp
        <div class="mb-6 flex flex-col items-stretch gap-4 sm:flex-row sm:items-center sm:justify-between md:mb-8">
            <div class="min-w-0">
                <p class="text-xs font-black uppercase tracking-wide text-primary">Order Details</p>
                <h1 class="mt-1 break-words text-2xl font-black text-ink md:text-3xl">{{ $order->order_number }}</h1>
                <p class="mt-2 text-sm text-gray-500">Placed on {{ $order->created_at->format('d M Y') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-3">
                @if($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later')
                    <a href="{{ route('orders.delivery-payment', $order) }}" class="col-span-2 inline-flex h-10 items-center justify-center gap-2 rounded-md bg-[#8a681b] px-4 text-center text-xs font-black text-white hover:bg-ink sm:col-span-1 sm:text-sm"><i class="fa-solid fa-wallet"></i> Pay Delivery</a>
                @endif
                <a href="{{ route('orders.receipt', $order) }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-3 text-center text-xs font-bold text-ink hover:border-primary hover:text-primary sm:px-4 sm:text-sm"><i class="fa-solid fa-receipt"></i> Receipt</a>
                <span class="inline-flex h-10 items-center justify-center rounded-md border px-3 text-center text-xs font-black capitalize sm:px-4 sm:text-sm {{ $statusStyle }}">{{ str($order->status)->replace('_', ' ') }}</span>
            </div>
        </div>

        <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_360px] lg:gap-6">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:p-6">
                <div class="mb-5 flex items-center gap-3 border-b border-gray-100 pb-4">
                    <span class="grid h-9 w-9 place-items-center rounded-md bg-[#eaf5f3] text-primary"><i class="fa-solid fa-box-open"></i></span>
                    <h2 class="text-lg font-black text-ink">Order Items</h2>
                </div>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex flex-col gap-2 border-b border-gray-100 pb-4 last:border-0 last:pb-0 sm:flex-row sm:justify-between sm:gap-4">
                            <div class="min-w-0">
                                <p class="font-semibold text-ink">{{ $item->product_name }}</p>
                                @if($item->selected_color_name)
                                    <p class="mt-1 flex items-center gap-2 text-xs font-semibold text-gray-500">
                                        <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: {{ $item->selected_color_hex ?: '#E5E7EB' }}"></span>
                                        Color: {{ $item->selected_color_name }}
                                    </p>
                                @endif
                                <p class="text-sm text-gray-500">BDT {{ number_format($item->unit_price, 2) }} x {{ $item->quantity }}</p>
                                @if($order->status === 'delivered' && $item->product_id)
                                    @php($review = $order->reviews->firstWhere('product_id', $item->product_id))
                                    <form method="POST" action="{{ route('orders.reviews.store', $order) }}" class="mt-4 rounded-md border border-gray-200 bg-[#f8f9fa] p-4">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <label class="mb-1 block text-sm font-semibold text-ink">Rate this product</label>
                                        <div class="grid gap-3 md:grid-cols-[140px_1fr_auto]">
                                            <select name="rating" class="rounded border-gray-200 text-sm" required>
                                                <option value="">Rating</option>
                                                @for($rating = 5; $rating >= 1; $rating--)
                                                    <option value="{{ $rating }}" @selected(old('rating', $review?->rating) == $rating)>{{ $rating }} out of 5</option>
                                                @endfor
                                            </select>
                                            <input name="comment" value="{{ old('comment', $review?->comment) }}" placeholder="Write a short comment" class="rounded border-gray-200 text-sm">
                                            <button class="rounded bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-ink">{{ $review ? 'Update' : 'Submit' }}</button>
                                        </div>
                                        @error('rating')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                        @error('comment')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </form>
                                @endif
                            </div>
                            <p class="shrink-0 font-semibold sm:text-right">BDT {{ number_format($item->total, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <aside class="h-fit overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm lg:sticky lg:top-8">
                <div class="flex items-center gap-3 border-b border-gray-100 bg-[#f8f9fa] px-5 py-4">
                    <span class="grid h-9 w-9 place-items-center rounded-md bg-ink text-white"><i class="fa-solid fa-location-dot"></i></span>
                    <h2 class="text-lg font-black text-ink">Delivery</h2>
                </div>
                <div class="p-5">
                <p class="font-semibold">{{ $order->customer_name }}</p>
                <p class="text-sm text-gray-600">{{ $order->mobile }}</p>
                <p class="mt-3 text-sm text-gray-600">{{ $order->address }}, {{ $order->city }}</p>
                @if($order->advance_delivery_required)
                    <div class="mt-5 rounded border border-accent/40 bg-accent/10 p-3 text-sm sm:p-4">
                        <p class="font-bold text-ink">Advance Delivery Charge</p>
                        <p class="mt-1">Area: {{ $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Inside Dhaka' }}</p>
                        <p>Charge: BDT {{ number_format($order->shipping, 2) }}</p>
                        <p>Option: {{ $order->delivery_charge_payment_option === 'pay_later' ? 'Pay Later' : 'Paid Now' }}</p>
                        @if($order->delivery_charge_payment_option === 'pay_now')
                            <p>Method: {{ $order->delivery_payment_method }}</p>
                            <p>Payment Mobile: {{ $order->delivery_payment_mobile }}</p>
                            <p>Transaction ID: {{ $order->delivery_transaction_id }}</p>
                            @if($order->delivery_payment_proof)
                                <p class="mt-2 inline-flex items-center gap-2 rounded bg-green-50 px-2 py-1 font-semibold text-green-700"><i class="fa-solid fa-circle-check"></i> Payment screenshot submitted</p>
                            @endif
                        @else
                            <p class="mt-2 rounded bg-red-50 p-2 font-semibold text-red-700">ডেলিভারি চার্জ পরিশোধ না করা পর্যন্ত আপনার অর্ডার সম্পূর্ণভাবে কনফার্ম হবে না।</p>
                        @endif
                    </div>
                @endif
                <div class="mt-5 space-y-2 border-t border-gray-100 pt-5 text-sm">
                    <div class="flex justify-between gap-4"><span>Order total</span><span class="font-bold">BDT {{ number_format($order->total, 2) }}</span></div>
                    <div class="flex justify-between gap-4 text-green-700"><span>Paid amount</span><span class="font-bold">BDT {{ number_format($order->paidAmount(), 2) }}</span></div>
                    <div class="flex justify-between gap-4 text-lg font-black text-ink"><span>Due amount</span><span>BDT {{ number_format($order->dueAmount(), 2) }}</span></div>
                </div>
                </div>
            </aside>
        </div>
    </section>
</x-user-dashboard-layout>
