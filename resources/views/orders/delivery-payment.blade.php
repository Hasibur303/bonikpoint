<x-user-dashboard-layout>
    <section class="max-w-4xl">
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-wide text-primary">Delivery Charge Payment</p>
            <h1 class="text-4xl font-black text-ink">{{ $order->order_number }}</h1>
            <p class="mt-2 text-gray-600">Pay the delivery charge and submit your transaction details so admin can confirm your order.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
            <form method="POST" action="{{ route('orders.delivery-payment.update', $order) }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                @csrf
                @method('PATCH')

                <div class="rounded border border-accent/40 bg-accent/10 p-4">
                    <p class="font-bold text-ink">Payment instruction: send money to this number</p>
                    <div class="mt-3 grid gap-2 text-sm text-gray-700 md:grid-cols-3">
                        <p><span class="font-semibold">Bkash:</span> {{ $settings['bkash_number'] }}</p>
                        <p><span class="font-semibold">Nagad:</span> {{ $settings['nagad_number'] }}</p>
                        <p><span class="font-semibold">Rocket:</span> {{ $settings['rocket_number'] }}</p>
                    </div>
                </div>

                <div class="mt-5 grid gap-5 md:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Payment Method</label>
                        <select name="delivery_payment_method" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                            <option value="">Select</option>
                            @foreach(['Bkash', 'Nagad', 'Rocket'] as $method)
                                <option value="{{ $method }}" @selected(old('delivery_payment_method', $order->delivery_payment_method) === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                        @error('delivery_payment_method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Payment Mobile Number</label>
                        <input name="delivery_payment_mobile" value="{{ old('delivery_payment_mobile', $order->delivery_payment_mobile) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('delivery_payment_mobile')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Transaction ID</label>
                        <input name="delivery_transaction_id" value="{{ old('delivery_transaction_id', $order->delivery_transaction_id) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('delivery_transaction_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button class="mt-6 rounded-lg bg-primary px-8 py-3 font-semibold text-white hover:bg-ink">Submit Payment</button>
            </form>

            <aside class="h-fit rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="text-xl font-black text-ink">Amount Due</h2>
                <div class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><span>Area</span><span>{{ $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Inside Dhaka' }}</span></div>
                    <div class="flex justify-between text-lg font-black text-primary"><span>Delivery Charge</span><span>BDT {{ number_format($order->shipping, 2) }}</span></div>
                </div>
                <p class="mt-5 rounded bg-red-50 p-3 text-sm font-semibold text-red-700">{{ $settings['delivery_pay_later_note_bn'] }}</p>
            </aside>
        </div>
    </section>
</x-user-dashboard-layout>
