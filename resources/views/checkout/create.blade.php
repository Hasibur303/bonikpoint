<x-app-layout>
    <section class="container py-12">
        <h1 class="mb-8 text-4xl font-black text-ink">Checkout</h1>
        <div class="grid gap-8 lg:grid-cols-[1fr_360px]">
            <form method="POST" action="{{ route('checkout.store') }}" class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Name</label>
                        <input name="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Email</label>
                        <input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Mobile</label>
                        <input name="mobile" value="{{ old('mobile', auth()->user()->mobile) }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('mobile')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">City</label>
                        <input name="city" value="{{ old('city') }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>
                        @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-semibold">Address</label>
                        <textarea name="address" rows="4" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary" required>{{ old('address') }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-semibold">Order Notes</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary">{{ old('notes') }}</textarea>
                    </div>

                    <div class="md:col-span-2 rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <h2 class="font-black text-ink">রিটার্ন পলিসি</h2>
                        <p class="mt-2 text-sm leading-7 text-gray-700">
                            ডেলিভারির সময় ডেলিভারি ম্যানের সামনে পণ্য খুলে দেখে নিন। ভুল, ক্ষতিগ্রস্ত, অসম্পূর্ণ, বা অর্ডারের সাথে না মিললে
                            সঙ্গে সঙ্গে ছবি/ভিডিও প্রমাণ নিয়ে ডেলিভারি ম্যানের কাছেই রিটার্ন করুন। ডেলিভারি ম্যান চলে যাওয়ার পর রিটার্ন গ্রহণযোগ্য নাও হতে পারে।
                        </p>
                        <a href="{{ route('return-policy') }}" class="mt-3 inline-block text-sm font-bold text-primary hover:text-ink">সম্পূর্ণ পলিসি দেখুন</a>
                    </div>

                    @if($advanceDeliveryRequired)
                        <div class="md:col-span-2 rounded-lg border border-accent/40 bg-accent/10 p-4">
                            <h2 class="text-lg font-black text-ink">Advance Delivery Charge Required</h2>
                            <p class="mt-2 text-sm text-gray-700">Some products in your cart require advance delivery charge before order confirmation.</p>

                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                <label class="rounded bg-white p-3 ring-1 ring-gray-100">
                                    <input type="radio" name="delivery_area" value="inside_dhaka" data-delivery-charge="{{ $deliverySettings['inside_dhaka_delivery_charge'] }}" @checked(old('delivery_area', 'inside_dhaka') === 'inside_dhaka')>
                                    <span class="ml-2 font-semibold">Inside Dhaka - BDT {{ number_format($deliverySettings['inside_dhaka_delivery_charge'], 2) }}</span>
                                </label>
                                <label class="rounded bg-white p-3 ring-1 ring-gray-100">
                                    <input type="radio" name="delivery_area" value="outside_dhaka" data-delivery-charge="{{ $deliverySettings['outside_dhaka_delivery_charge'] }}" @checked(old('delivery_area') === 'outside_dhaka')>
                                    <span class="ml-2 font-semibold">Outside Dhaka - BDT {{ number_format($deliverySettings['outside_dhaka_delivery_charge'], 2) }}</span>
                                </label>
                            </div>
                            @error('delivery_area')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                            <div class="mt-4 rounded bg-white p-4 ring-1 ring-gray-100">
                                <p class="font-bold text-ink">Payment instruction: send money to this number</p>
                                <div class="mt-2 grid gap-2 text-sm text-gray-700 md:grid-cols-3">
                                    <p><span class="font-semibold">Bkash:</span> {{ $deliverySettings['bkash_number'] }}</p>
                                    <p><span class="font-semibold">Nagad:</span> {{ $deliverySettings['nagad_number'] }}</p>
                                    <p><span class="font-semibold">Rocket:</span> {{ $deliverySettings['rocket_number'] }}</p>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                <label class="rounded bg-white p-3 ring-1 ring-gray-100">
                                    <input type="radio" name="delivery_charge_payment_option" value="pay_now" @checked(old('delivery_charge_payment_option', 'pay_now') === 'pay_now')>
                                    <span class="ml-2 font-semibold">I paid delivery charge now</span>
                                </label>
                                <label class="rounded bg-white p-3 ring-1 ring-gray-100">
                                    <input type="radio" name="delivery_charge_payment_option" value="pay_later" @checked(old('delivery_charge_payment_option') === 'pay_later')>
                                    <span class="ml-2 font-semibold">Delivery charge pay later</span>
                                </label>
                            </div>
                            @error('delivery_charge_payment_option')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                            <div id="delivery-payment-fields" class="mt-4 grid gap-4 md:grid-cols-3">
                                <div>
                                    <label class="mb-1 block text-sm font-semibold">Payment Method</label>
                                    <select name="delivery_payment_method" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                                        <option value="">Select</option>
                                        @foreach(['Bkash', 'Nagad', 'Rocket'] as $method)
                                            <option value="{{ $method }}" @selected(old('delivery_payment_method') === $method)>{{ $method }}</option>
                                        @endforeach
                                    </select>
                                    @error('delivery_payment_method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold">Payment Mobile Number</label>
                                    <input name="delivery_payment_mobile" value="{{ old('delivery_payment_mobile') }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                                    @error('delivery_payment_mobile')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-semibold">Transaction ID</label>
                                    <input name="delivery_transaction_id" value="{{ old('delivery_transaction_id') }}" class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary">
                                    @error('delivery_transaction_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div id="delivery-pay-later-note" class="mt-4 hidden rounded border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-700">
                                {{ $deliverySettings['delivery_pay_later_note_bn'] }}
                            </div>
                        </div>
                    @endif
                </div>
                <button class="mt-6 rounded-lg bg-primary px-8 py-3 font-semibold text-white hover:bg-ink">Place Order</button>
            </form>
            <aside class="h-fit rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="text-xl font-black text-ink">Your Order</h2>
                <div class="mt-5 space-y-3">
                    @foreach($cartItems as $item)
                        <div class="flex justify-between gap-4 text-sm">
                            <span>{{ $item['product']->name }} x {{ $item['quantity'] }}</span>
                            <span>BDT {{ number_format($item['total'], 2) }}</span>
                        </div>
                    @endforeach
                    @if($advanceDeliveryRequired)
                        <div class="flex justify-between gap-4 text-sm">
                            <span>Advance delivery charge</span>
                            <span id="delivery-charge-summary">BDT {{ number_format($shipping, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t pt-3 flex justify-between text-lg font-black text-ink"><span>Total</span><span id="checkout-total">BDT {{ number_format($subtotal + ($advanceDeliveryRequired ? $shipping : 0), 2) }}</span></div>
                </div>
            </aside>
        </div>
    </section>

    @if($advanceDeliveryRequired)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const subtotal = {{ (float) $subtotal }};
                const chargeSummary = document.getElementById('delivery-charge-summary');
                const total = document.getElementById('checkout-total');
                const paymentFields = document.getElementById('delivery-payment-fields');
                const payLaterNote = document.getElementById('delivery-pay-later-note');
                const money = (value) => `BDT ${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

                const refreshCharge = () => {
                    const selectedArea = document.querySelector('input[name="delivery_area"]:checked');
                    const charge = Number(selectedArea?.dataset.deliveryCharge || 60);
                    chargeSummary.textContent = money(charge);
                    total.textContent = money(subtotal + charge);
                };

                const refreshPaymentMode = () => {
                    const selectedOption = document.querySelector('input[name="delivery_charge_payment_option"]:checked')?.value;
                    const payLater = selectedOption === 'pay_later';
                    paymentFields.classList.toggle('hidden', payLater);
                    payLaterNote.classList.toggle('hidden', !payLater);
                };

                document.querySelectorAll('input[name="delivery_area"]').forEach((input) => input.addEventListener('change', refreshCharge));
                document.querySelectorAll('input[name="delivery_charge_payment_option"]').forEach((input) => input.addEventListener('change', refreshPaymentMode));

                refreshCharge();
                refreshPaymentMode();
            });
        </script>
    @endif
</x-app-layout>
