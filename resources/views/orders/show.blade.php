<x-user-dashboard-layout>
    <section>
        <div class="mb-6 flex flex-col items-stretch gap-4 sm:flex-row sm:items-center sm:justify-between md:mb-8">
            <div class="min-w-0">
                <p class="text-sm font-bold uppercase tracking-wide text-primary">Order Details</p>
                <h1 class="break-words text-2xl font-black text-ink md:text-4xl">{{ $order->order_number }}</h1>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center sm:gap-3">
                @if($order->advance_delivery_required && $order->delivery_charge_payment_option === 'pay_later')
                    <a href="{{ route('orders.delivery-payment', $order) }}" class="col-span-2 inline-flex items-center justify-center rounded-full bg-red-600 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-red-700 sm:col-span-1 sm:px-4 sm:text-sm">Pay Delivery Charge</a>
                @endif
                <a href="{{ route('orders.receipt', $order) }}" class="inline-flex items-center justify-center rounded-full border border-gray-200 px-3 py-2 text-center text-xs font-semibold text-ink hover:border-primary hover:text-primary sm:px-4 sm:text-sm">Receipt</a>
                <span class="inline-flex items-center justify-center rounded-full bg-gray-100 px-3 py-2 text-center text-xs font-semibold capitalize sm:px-4 sm:text-sm">{{ str($order->status)->replace('_', ' ') }}</span>
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100 sm:p-6">
                <h2 class="mb-5 text-xl font-black text-ink">Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex flex-col gap-2 border-b pb-4 sm:flex-row sm:justify-between sm:gap-4">
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
                                    <form method="POST" action="{{ route('orders.reviews.store', $order) }}" class="mt-4 rounded bg-gray-50 p-4">
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

            <aside class="h-fit rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100 sm:p-6">
                <h2 class="mb-4 text-xl font-black text-ink">Delivery</h2>
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
