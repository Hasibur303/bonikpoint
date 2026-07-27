@section('title', ($isGuestCheckout ? 'Guest Checkout' : 'Checkout').' | Bonik Point')
@section('meta_description', 'Enter your delivery information and confirm your Bonik Point order securely.')
@section('canonical', $isGuestCheckout ? route('guest.checkout.create') : route('checkout.create'))
@section('robots', 'noindex,follow')

<x-app-layout>
    <section class="bg-[#f4f7f6] py-4 md:py-12">
        <div class="container">
            <div class="mb-5 flex items-end justify-between gap-4 md:mb-8">
                <div>
                    <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-primary hover:text-ink sm:text-sm">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back to cart
                    </a>
                    <p class="mt-3 text-[11px] font-bold uppercase tracking-wide text-primary sm:mt-5 sm:text-xs">Secure order details</p>
                    <h1 class="mt-1 text-2xl font-black uppercase text-ink sm:mt-2 sm:text-4xl">Checkout</h1>
                </div>
                <div class="hidden items-center gap-3 text-sm sm:flex">
                    <span class="grid h-10 w-10 place-items-center rounded-md bg-ink text-white"><i class="fa-solid fa-bag-shopping"></i></span>
                    <div>
                        <p class="font-black text-ink">{{ count($cartItems) }} {{ Str::plural('item', count($cartItems)) }}</p>
                        <p class="text-xs text-gray-500">Ready to place order</p>
                    </div>
                </div>
            </div>

            <div class="grid items-start gap-4 lg:grid-cols-[minmax(0,1fr)_390px] lg:gap-6">
                <form method="POST" action="{{ route($isGuestCheckout ? 'guest.checkout.store' : 'checkout.store') }}" enctype="multipart/form-data" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                    @csrf

                    <div class="border-b border-gray-100 px-4 py-4 sm:px-7 sm:py-5">
                        <h2 class="text-lg font-black uppercase text-ink sm:text-xl">{{ $isGuestCheckout ? 'Guest checkout details' : 'Customer and delivery details' }}</h2>
                        @if($isGuestCheckout)
                            <p class="mt-1 text-xs leading-5 text-gray-500 sm:text-sm">You can place this order without creating an account. Delivery charge pay later is not available for guest checkout.</p>
                        @endif
                    </div>

                    <div class="grid gap-3 p-4 sm:grid-cols-2 sm:gap-4 sm:p-6">
                        <div>
                            <label for="checkout-name" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Full name</label>
                            <input id="checkout-name" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" autocomplete="name" class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 sm:h-11" required>
                            @error('customer_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="checkout-mobile" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Mobile number</label>
                            <input id="checkout-mobile" name="mobile" value="{{ old('mobile', auth()->user()?->mobile) }}" autocomplete="tel" class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 sm:h-11" required>
                            @error('mobile')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="checkout-email" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Email address</label>
                            <input id="checkout-email" name="email" type="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 sm:h-11" required>
                            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="checkout-city" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">City or district</label>
                            <div class="relative">
                                <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-sm text-primary"></i>
                                <input id="checkout-city" name="city" list="bangladesh-cities" value="{{ old('city') }}" placeholder="Search city or district" autocomplete="address-level2" class="h-10 w-full rounded-md border-gray-200 bg-[#f8faf9] pl-10 pr-4 text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 sm:h-11" required>
                            </div>
                            <datalist id="bangladesh-cities">
                                @foreach($cities as $city)
                                    <option value="{{ $city }}"></option>
                                @endforeach
                            </datalist>
                            @error('city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="checkout-address" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Full delivery address</label>
                            <textarea id="checkout-address" name="address" rows="2" autocomplete="street-address" placeholder="House, road, area, and any delivery details" class="w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20" required>{{ old('address') }}</textarea>
                            @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="checkout-notes" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Order note <span class="font-medium text-gray-400">(optional)</span></label>
                            <textarea id="checkout-notes" name="notes" rows="1" placeholder="Add a helpful note for the delivery team" class="w-full rounded-md border-gray-200 bg-[#f8faf9] text-sm shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    @if($advanceDeliveryRequired)
                        <div class="border-y border-gray-100 px-4 py-4 sm:px-7 sm:py-5">
                            <h2 class="text-lg font-black uppercase text-ink sm:text-xl">Advance delivery charge</h2>
                        </div>
                        <div class="p-4 sm:p-7">
                            <div id="delivery-charge-card" class="rounded-lg border border-primary/20 bg-[#edf7f6] p-3 sm:p-4">
                                <div class="flex gap-3">
                                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md bg-primary text-white sm:h-10 sm:w-10"><i class="fa-solid fa-truck-fast"></i></span>
                                    <div>
                                        <p id="delivery-city-label" class="text-sm font-black text-ink sm:text-base">Choose a city to calculate delivery</p>
                                        <p class="mt-1 text-xs leading-5 text-gray-600 sm:text-sm sm:leading-6">Dhaka BDT {{ number_format($deliverySettings['inside_dhaka_delivery_charge']) }}. Outside Dhaka BDT {{ number_format($deliverySettings['outside_dhaka_delivery_charge']) }}.</p>
                                        <p class="mt-2 text-xs font-bold text-primary sm:text-sm">অর্ডার কনফার্ম করতে অগ্রিম ডেলিভারি চার্জ দিতে হবে।</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 rounded-lg border border-gray-200 bg-[#f7f9f8] p-3 sm:mt-5 sm:p-4">
                                <p class="text-sm font-black text-ink sm:text-base">Payment instruction</p>
                                <p id="delivery-payment-instruction" class="mt-1 text-xs text-gray-600 sm:text-sm">Choose a city to see the required delivery payment amount.</p>
                                <div id="delivery-payment-account-panel" class="mt-3">
                                    <label for="delivery-payment-account" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Send money account</label>
                                    <select id="delivery-payment-account" name="delivery_payment_method" class="h-10 w-full rounded-md border-gray-200 bg-white py-0 text-sm shadow-sm focus:border-primary focus:ring-primary sm:h-11">
                                        <option value="">Select payment number</option>
                                        <option value="Bkash" data-number="{{ $deliverySettings['bkash_number'] }}" @selected(old('delivery_payment_method') === 'Bkash')>Bkash - {{ $deliverySettings['bkash_number'] }}</option>
                                        <option value="Nagad" data-number="{{ $deliverySettings['nagad_number'] }}" @selected(old('delivery_payment_method') === 'Nagad')>Nagad - {{ $deliverySettings['nagad_number'] }}</option>
                                        <option value="Rocket" data-number="{{ $deliverySettings['rocket_number'] }}" @selected(old('delivery_payment_method') === 'Rocket')>Rocket - {{ $deliverySettings['rocket_number'] }}</option>
                                    </select>
                                    @error('delivery_payment_method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    <div class="mt-2 flex min-h-9 items-center justify-between gap-3 rounded-md border border-primary/15 bg-white px-3 py-2">
                                        <p id="selected-payment-number" class="min-w-0 text-xs font-semibold text-primary">Select where you sent the delivery charge.</p>
                                        <button id="copy-payment-number" type="button" class="hidden shrink-0 items-center gap-1.5 rounded-md bg-primary px-3 py-1.5 text-xs font-bold text-white hover:bg-ink">
                                            <i class="fa-regular fa-copy"></i>
                                            <span>Copy</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @if($isGuestCheckout)
                                <input type="hidden" name="delivery_charge_payment_option" value="pay_now">
                                <div class="mt-4 rounded-lg border border-primary/20 bg-white p-3 sm:mt-5 sm:p-4">
                                    <p class="text-sm font-black text-ink sm:text-base">Guest checkout requires advance delivery charge payment now.</p>
                                    <p class="mt-1 text-xs leading-5 text-gray-500 sm:text-sm">To keep guest orders reliable, pay-later is available only for signed-in customers.</p>
                                </div>
                            @else
                                <div class="mt-4 grid gap-2 sm:mt-5 sm:grid-cols-2 sm:gap-3">
                                    <label class="cursor-pointer rounded-lg border bg-white p-3 transition has-[:checked]:border-primary has-[:checked]:ring-1 has-[:checked]:ring-primary sm:p-4">
                                        <input type="radio" name="delivery_charge_payment_option" value="pay_now" @checked(old('delivery_charge_payment_option', 'pay_now') === 'pay_now')>
                                        <span class="ml-2 text-sm font-black text-ink sm:text-base">Pay delivery charge now</span>
                                        <span class="mt-1 block pl-6 text-xs text-gray-500">Submit your payment details now.</span>
                                    </label>
                                    <label class="cursor-pointer rounded-lg border bg-white p-3 transition has-[:checked]:border-primary has-[:checked]:ring-1 has-[:checked]:ring-primary sm:p-4">
                                        <input type="radio" name="delivery_charge_payment_option" value="pay_later" @checked(old('delivery_charge_payment_option') === 'pay_later')>
                                        <span class="ml-2 text-sm font-black text-ink sm:text-base">Pay later</span>
                                        <span class="mt-1 block pl-6 text-xs text-gray-500">Order stays pending until payment is submitted.</span>
                                    </label>
                                </div>
                            @endif
                            @error('delivery_charge_payment_option')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror

                            <div id="delivery-payment-fields" class="mt-4 grid gap-3 rounded-lg border border-gray-200 bg-[#f1f6f5] p-3 sm:mt-5 sm:grid-cols-2 sm:gap-4 sm:p-4">
                                <div>
                                    <label for="delivery-payment-mobile" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Payment mobile <span class="font-medium text-gray-400">(or screenshot)</span></label>
                                    <input id="delivery-payment-mobile" name="delivery_payment_mobile" value="{{ old('delivery_payment_mobile') }}" inputmode="tel" class="h-10 w-full rounded-md border-gray-200 bg-white text-sm shadow-sm focus:border-primary focus:ring-primary sm:h-11">
                                    @error('delivery_payment_mobile')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="delivery-transaction-id" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Transaction ID <span class="font-medium text-gray-400">(or screenshot)</span></label>
                                    <input id="delivery-transaction-id" name="delivery_transaction_id" value="{{ old('delivery_transaction_id') }}" class="h-10 w-full rounded-md border-gray-200 bg-white text-sm shadow-sm focus:border-primary focus:ring-primary sm:h-11">
                                    @error('delivery_transaction_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="delivery-payment-proof" class="mb-1.5 block text-xs font-bold text-ink sm:text-sm">Payment screenshot <span class="font-medium text-gray-400">(or payment details)</span></label>
                                    <input id="delivery-payment-proof" name="delivery_payment_proof" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-md border border-gray-200 bg-white text-xs text-gray-600 shadow-sm file:mr-3 file:border-0 file:bg-primary/10 file:px-4 file:py-2.5 file:font-bold file:text-primary hover:file:bg-primary hover:file:text-white sm:text-sm">
                                    <p class="mt-1.5 text-[11px] text-gray-500 sm:text-xs">Upload a screenshot, or enter both payment mobile number and transaction ID. JPG, PNG or WebP, maximum 5 MB.</p>
                                    @error('delivery_payment_proof')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div id="delivery-pay-later-note" class="mt-4 hidden rounded-lg border border-red-200 bg-red-50 p-3 text-xs font-semibold leading-5 text-red-700 sm:mt-5 sm:p-4 sm:text-sm">
                                {{ $deliverySettings['delivery_pay_later_note_bn'] }}
                            </div>
                        </div>
                    @endif

                    <div class="sticky bottom-0 z-20 border-t border-gray-100 bg-white/95 p-4 shadow-[0_-12px_30px_rgba(15,23,42,0.08)] backdrop-blur sm:static sm:px-7 sm:py-5 sm:shadow-none">
                        @if($isGuestCheckout)
                            <x-bot-protection />
                        @endif
                        <div class="mb-3 flex items-center justify-between gap-2 rounded-md bg-[#f4f7f6] p-2 sm:hidden">
                            <span class="text-xs font-black text-ink">Need help?</span>
                            <div class="flex items-center gap-1.5">
                                <a href="tel:01540381020" class="grid h-9 w-9 place-items-center rounded-md bg-primary text-white" title="Call customer service" aria-label="Call customer service">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </a>
                                <a href="https://wa.me/8801540381020" target="_blank" rel="noopener" class="grid h-9 w-9 place-items-center rounded-md bg-green-600 text-white" title="WhatsApp customer service" aria-label="WhatsApp customer service">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                </a>
                                <a href="{{ route('order-instructions') }}" class="grid h-9 w-9 place-items-center rounded-md bg-ink text-white" title="Bangla order instructions" aria-label="Bangla order instructions">
                                    <i class="fa-solid fa-circle-info text-xs"></i>
                                </a>
                            </div>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-gray-500">Please check your delivery details before placing the order.</p>
                        <button class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-md bg-primary px-6 text-sm font-black text-white hover:bg-ink sm:h-12">
                            Place order
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        </div>
                    </div>
                </form>

                <aside class="lg:sticky lg:top-28">
                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="border-b border-gray-100 px-5 py-5">
                            <p class="text-xs font-bold uppercase tracking-wide text-primary">Order summary</p>
                            <h2 class="mt-1 text-xl font-black uppercase text-ink">Your items</h2>
                        </div>
                        <div class="divide-y divide-gray-100 px-5">
                            @foreach($cartItems as $item)
                                <div class="grid grid-cols-[56px_1fr_auto] items-center gap-3 py-4">
                                    <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" width="56" height="56" loading="lazy" decoding="async" class="h-14 w-14 rounded-md bg-[#f7f9f8] object-contain p-1">
                                    <div class="min-w-0">
                                        <p class="line-clamp-2 text-sm font-bold leading-5 text-ink">{{ $item['product']->name }}</p>
                                        @if($item['product_color_name'])
                                            <p class="mt-1 flex items-center gap-1.5 text-xs font-semibold text-gray-500">
                                                <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: {{ $item['product_color_hex'] ?: '#E5E7EB' }}"></span>
                                                {{ $item['product_color_name'] }}
                                            </p>
                                        @endif
                                        @if($item['product_flavor_name'])
                                            <p class="mt-1 text-xs font-semibold text-gray-500">Flavor: {{ $item['product_flavor_name'] }}</p>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-500">Qty {{ $item['quantity'] }}</p>
                                    </div>
                                    <span class="text-right text-sm font-black text-ink">BDT {{ number_format($item['total']) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="space-y-3 border-t border-gray-100 bg-[#f7f9f8] p-5 text-sm">
                            <div class="flex justify-between gap-4 text-gray-600"><span>Subtotal</span><span class="font-bold text-ink">BDT {{ number_format($subtotal) }}</span></div>
                            @if($advanceDeliveryRequired)
                                <div class="flex justify-between gap-4 text-gray-600"><span>Advance delivery</span><span id="delivery-charge-summary" class="font-bold text-ink">{{ $shipping ? 'BDT '.number_format($shipping) : 'Select city' }}</span></div>
                            @endif
                            <div class="flex justify-between gap-4 border-t border-gray-200 pt-4 text-lg font-black text-ink"><span>Total</span><span id="checkout-total">BDT {{ number_format($subtotal + ($advanceDeliveryRequired ? $shipping : 0)) }}</span></div>
                        </div>
                    </div>
                    <div class="mt-4 hidden rounded-lg border border-primary/15 bg-white p-4 shadow-sm sm:block">
                        <div class="flex gap-3">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-primary/10 text-primary">
                                <i class="fa-solid fa-headset"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="font-black text-ink">Need help placing order?</p>
                                <p class="mt-1 text-xs leading-5 text-gray-600">If payment, delivery charge, or order details are confusing, call us before confirming.</p>
                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <a href="tel:01540381020" class="inline-flex items-center justify-center gap-2 rounded-md bg-primary px-3 py-2 text-xs font-bold text-white hover:bg-ink">
                                        <i class="fa-solid fa-phone"></i>
                                        Call
                                    </a>
                                    <a href="https://wa.me/8801540381020" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 rounded-md border border-primary/20 px-3 py-2 text-xs font-bold text-primary hover:border-primary hover:bg-primary hover:text-white">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        WhatsApp
                                    </a>
                                    <a href="{{ route('order-instructions') }}" class="col-span-2 inline-flex items-center justify-center gap-2 rounded-md bg-ink px-3 py-2 text-xs font-bold text-white hover:bg-primary">
                                        <i class="fa-solid fa-circle-info"></i>
                                        বাংলা নির্দেশনা
                                    </a>
                                </div>
                                <p class="mt-2 text-[11px] font-semibold text-gray-500">24 hours customer service: 01540381020</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @if($advanceDeliveryRequired)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const subtotal = {{ (float) $subtotal }};
                const dhakaCharge = {{ (int) $deliverySettings['inside_dhaka_delivery_charge'] }};
                const outsideDhakaCharge = {{ (int) $deliverySettings['outside_dhaka_delivery_charge'] }};
                const cityInput = document.getElementById('checkout-city');
                const cityLabel = document.getElementById('delivery-city-label');
                const chargeSummary = document.getElementById('delivery-charge-summary');
                const total = document.getElementById('checkout-total');
                const paymentInstruction = document.getElementById('delivery-payment-instruction');
                const paymentAccount = document.getElementById('delivery-payment-account');
                const paymentAccountPanel = document.getElementById('delivery-payment-account-panel');
                const selectedPaymentNumber = document.getElementById('selected-payment-number');
                const copyPaymentNumber = document.getElementById('copy-payment-number');
                const paymentFields = document.getElementById('delivery-payment-fields');
                const paymentProof = document.getElementById('delivery-payment-proof');
                const payLaterNote = document.getElementById('delivery-pay-later-note');
                const money = (value) => `BDT ${Number(value || 0).toLocaleString()}`;

                const copyText = async (value) => {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(value);
                        return;
                    }

                    const helper = document.createElement('textarea');
                    helper.value = value;
                    helper.style.position = 'fixed';
                    helper.style.opacity = '0';
                    document.body.appendChild(helper);
                    helper.select();
                    document.execCommand('copy');
                    helper.remove();
                };

                const refreshCharge = () => {
                    const city = cityInput.value.trim();

                    if (!city) {
                        cityLabel.textContent = 'Choose a city to calculate delivery';
                        chargeSummary.textContent = 'Select city';
                        total.textContent = money(subtotal);
                        paymentInstruction.textContent = 'Choose a city to see the required delivery payment amount.';
                        return;
                    }

                    const insideDhaka = city.toLowerCase() === 'dhaka';
                    const charge = insideDhaka ? dhakaCharge : outsideDhakaCharge;
                    cityLabel.textContent = `${insideDhaka ? 'Dhaka' : 'Outside Dhaka'} delivery: ${money(charge)}`;
                    chargeSummary.textContent = money(charge);
                    total.textContent = money(subtotal + charge);
                    paymentInstruction.textContent = `Send ${money(charge)} for advance delivery charge to the selected mobile account.`;
                };

                const refreshPaymentAccount = () => {
                    if (!paymentAccount || !selectedPaymentNumber) {
                        return;
                    }

                    const selected = paymentAccount.options[paymentAccount.selectedIndex];
                    const number = selected?.dataset?.number;
                    selectedPaymentNumber.textContent = number
                        ? `Selected ${selected.value}: ${number}`
                        : 'Select where you sent the delivery charge.';
                    copyPaymentNumber?.classList.toggle('hidden', !number);
                    copyPaymentNumber?.classList.toggle('inline-flex', Boolean(number));
                    if (copyPaymentNumber) {
                        copyPaymentNumber.dataset.number = number || '';
                    }
                };

                const refreshPaymentMode = () => {
                    const payLater = document.querySelector('input[name="delivery_charge_payment_option"]:checked')?.value === 'pay_later';
                    paymentFields.classList.toggle('hidden', payLater);
                    paymentAccountPanel.classList.toggle('hidden', payLater);
                    payLaterNote.classList.toggle('hidden', !payLater);
                    if (paymentProof) {
                        paymentProof.disabled = payLater;
                    }
                };

                cityInput.addEventListener('input', refreshCharge);
                cityInput.addEventListener('change', refreshCharge);
                paymentAccount?.addEventListener('change', refreshPaymentAccount);
                copyPaymentNumber?.addEventListener('click', async () => {
                    const number = copyPaymentNumber.dataset.number;
                    if (!number) {
                        return;
                    }

                    await copyText(number);
                    const label = copyPaymentNumber.querySelector('span');
                    label.textContent = 'Copied';
                    window.setTimeout(() => label.textContent = 'Copy', 1600);
                });
                document.querySelectorAll('input[name="delivery_charge_payment_option"]').forEach((input) => input.addEventListener('change', refreshPaymentMode));

                refreshCharge();
                refreshPaymentAccount();
                refreshPaymentMode();
            });
        </script>
    @endif
</x-app-layout>
