<x-user-dashboard-layout>
    <section class="max-w-5xl">
        <div class="mb-6 border-l-4 border-primary pl-4 sm:mb-8 sm:pl-5">
            <div class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-primary">
                <i class="fa-solid fa-shield-halved"></i>
                Delivery Charge Payment
            </div>
            <h1 class="mt-1 break-words text-2xl font-black text-ink sm:text-4xl">{{ $order->order_number }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600 sm:text-base">Pay the delivery charge and submit your transaction details so admin can confirm your order.</p>
        </div>

        <div class="grid items-start gap-5 lg:grid-cols-[minmax(0,1fr)_320px] lg:gap-6">
            <form method="POST" action="{{ route('orders.delivery-payment.update', $order) }}" enctype="multipart/form-data" class="overflow-hidden rounded-lg border border-[#c9d8d5] bg-[#fbfdfc] shadow-[0_12px_32px_rgba(16,63,68,0.10)]">
                @csrf
                @method('PATCH')

                <div class="bg-ink p-4 text-white sm:p-6">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-primary text-white"><i class="fa-solid fa-mobile-screen-button"></i></span>
                        <div>
                            <p class="font-black">Select a payment account</p>
                            <p class="mt-0.5 text-xs text-gray-300">Use Send Money, then copy the transaction ID.</p>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-2 text-sm text-ink md:grid-cols-3">
                        @foreach(['Bkash' => $settings['bkash_number'], 'Nagad' => $settings['nagad_number'], 'Rocket' => $settings['rocket_number']] as $method => $number)
                            <div class="flex items-center justify-between gap-2 rounded-md bg-white px-3 py-2.5 shadow-sm">
                                <p class="min-w-0"><span class="block text-[11px] font-bold uppercase text-gray-400">{{ $method }}</span> <span class="break-all font-black">{{ $number }}</span></p>
                                <button type="button" data-copy-number="{{ $number }}" class="copy-payment-number grid h-9 w-9 shrink-0 place-items-center rounded-md bg-[#e4f3f1] text-primary hover:bg-primary hover:text-white" title="Copy {{ $method }} number" aria-label="Copy {{ $method }} number">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 sm:p-6">
                    <div class="mb-5 flex items-center gap-3 border-b border-gray-200 pb-4">
                        <span class="grid h-9 w-9 place-items-center rounded-md bg-primary/10 text-primary"><i class="fa-solid fa-receipt"></i></span>
                        <div>
                            <h2 class="font-black text-ink">Payment details</h2>
                            <p class="text-xs text-gray-500">Upload a screenshot, or enter both payment mobile number and transaction ID.</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="delivery-payment-method" class="mb-1.5 block text-sm font-bold text-ink">Payment Method</label>
                            <select id="delivery-payment-method" name="delivery_payment_method" class="h-11 w-full rounded-md border-gray-200 bg-[#f2f7f6] text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            <option value="">Select</option>
                            @foreach(['Bkash', 'Nagad', 'Rocket'] as $method)
                                <option value="{{ $method }}" @selected(old('delivery_payment_method', $order->delivery_payment_method) === $method)>{{ $method }}</option>
                            @endforeach
                            </select>
                            @error('delivery_payment_method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="delivery-payment-mobile" class="mb-1.5 block text-sm font-bold text-ink">Payment Mobile Number <span class="font-medium text-gray-400">(or screenshot)</span></label>
                            <input id="delivery-payment-mobile" name="delivery_payment_mobile" value="{{ old('delivery_payment_mobile', $order->delivery_payment_mobile) }}" inputmode="tel" class="h-11 w-full rounded-md border-gray-200 bg-[#f2f7f6] text-sm shadow-sm focus:border-primary focus:ring-primary">
                            @error('delivery_payment_mobile')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="delivery-transaction-id" class="mb-1.5 block text-sm font-bold text-ink">Transaction ID <span class="font-medium text-gray-400">(or screenshot)</span></label>
                            <input id="delivery-transaction-id" name="delivery_transaction_id" value="{{ old('delivery_transaction_id', $order->delivery_transaction_id) }}" class="h-11 w-full rounded-md border-gray-200 bg-[#f2f7f6] text-sm shadow-sm focus:border-primary focus:ring-primary">
                            @error('delivery_transaction_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="mt-5 rounded-md border border-dashed border-[#a9c2bd] bg-[#edf5f3] p-4">
                        <label for="delivery-payment-proof" class="mb-1.5 flex items-center gap-2 text-sm font-bold text-ink"><i class="fa-regular fa-image text-primary"></i> Payment Screenshot <span class="font-normal text-gray-400">(or payment details)</span></label>
                        <input id="delivery-payment-proof" name="delivery_payment_proof" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-md border border-[#c9d8d5] bg-white text-xs text-gray-600 shadow-sm file:mr-3 file:border-0 file:bg-primary/10 file:px-4 file:py-3 file:font-bold file:text-primary hover:file:bg-primary hover:file:text-white sm:text-sm">
                        <p class="mt-1.5 text-xs text-gray-500">Upload a screenshot, or enter both payment mobile number and transaction ID. JPG, PNG or WebP, maximum 5 MB.</p>
                        @error('delivery_payment_proof')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <button class="mt-5 inline-flex h-12 w-full items-center justify-center gap-2 rounded-md bg-primary px-8 font-black text-white shadow-[0_8px_20px_rgba(8,124,127,0.24)] hover:bg-ink sm:w-auto">
                        Submit Payment
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </form>

            <aside class="h-fit overflow-hidden rounded-lg border border-[#ead9ad] bg-[#fff9e9] shadow-[0_12px_28px_rgba(93,72,24,0.10)] lg:sticky lg:top-8">
                <div class="bg-[#f4e6ba] px-5 py-4">
                    <div class="flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-md bg-[#8a681b] text-white"><i class="fa-solid fa-truck-fast"></i></span>
                        <div>
                            <p class="text-xs font-bold uppercase text-[#806523]">Payment required</p>
                            <h2 class="text-xl font-black text-ink">Amount Due</h2>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4 text-gray-600"><span>Delivery area</span><span class="font-bold text-ink">{{ $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Inside Dhaka' }}</span></div>
                        <div class="flex justify-between gap-4 border-t border-[#ead9ad] pt-4 text-lg font-black text-ink"><span>Delivery Charge</span><span class="text-primary">BDT {{ number_format($order->shipping, 2) }}</span></div>
                    </div>
                    <p class="mt-5 rounded-md border border-red-200 bg-red-50 p-3 text-sm font-semibold leading-6 text-red-700">{{ $settings['delivery_pay_later_note_bn'] }}</p>
                    <div class="mt-4 flex items-center gap-2 text-xs font-semibold text-gray-500">
                        <i class="fa-solid fa-lock text-primary"></i>
                        Your payment details are submitted securely.
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            document.querySelectorAll('.copy-payment-number').forEach((button) => {
                button.addEventListener('click', async () => {
                    await copyText(button.dataset.copyNumber);
                    button.innerHTML = '<i class="fa-solid fa-check"></i>';
                    window.setTimeout(() => button.innerHTML = '<i class="fa-regular fa-copy"></i>', 1600);
                });
            });
        });
    </script>
</x-user-dashboard-layout>
