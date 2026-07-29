@section('title', 'Track Order | Bonik Point')
@section('meta_description', 'Check your Bonik Point order status securely using your order number and mobile number.')
@section('canonical', route('guest.orders.track'))
@section('robots', 'noindex,follow')

<x-app-layout>
    <main class="min-h-[65vh] bg-[#f3f6f5] py-6 sm:py-10">
        <div class="container max-w-3xl">
            <section class="overflow-hidden rounded-lg border border-[#d8e3e0] bg-white shadow-[0_18px_45px_rgba(15,55,57,0.08)]">
                <div class="border-b border-[#e3ebe8] bg-[#edf5f3] p-5 sm:p-7">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-ink text-white"><i class="fa-solid fa-box"></i></span>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wide text-primary">Guest Order Support</p>
                            <h1 class="mt-1 text-2xl font-black text-ink sm:text-3xl">Check Order Status</h1>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-6 text-gray-600">আপনার Order Number এবং অর্ডারে দেওয়া Mobile Number লিখুন। নিরাপত্তার জন্য শুধু মোবাইল নম্বর দিয়ে অর্ডারের তথ্য দেখানো হয় না।</p>
                </div>

                <form method="POST" action="{{ route('guest.orders.track.lookup') }}" class="p-5 sm:p-7">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="track-order-number" class="mb-1.5 block text-sm font-black text-ink">Order Number</label>
                            <div class="relative">
                                <i class="fa-solid fa-receipt absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                <input id="track-order-number" name="order_number" value="{{ old('order_number') }}" placeholder="Example: BP-20260728..." class="h-11 w-full rounded-md border-gray-200 bg-[#f8faf9] pl-10 text-sm uppercase shadow-sm focus:border-primary focus:ring-primary" required>
                            </div>
                            @error('order_number')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="track-mobile" class="mb-1.5 block text-sm font-black text-ink">Mobile Number</label>
                            <div class="relative">
                                <i class="fa-solid fa-phone absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                <input id="track-mobile" name="mobile" value="{{ old('mobile') }}" placeholder="01XXXXXXXXX" inputmode="tel" autocomplete="tel" class="h-11 w-full rounded-md border-gray-200 bg-[#f8faf9] pl-10 text-sm shadow-sm focus:border-primary focus:ring-primary" required>
                            </div>
                            @error('mobile')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <x-bot-protection />

                    <button class="mt-5 inline-flex h-11 w-full items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white shadow-[0_4px_0_#075f62] transition hover:-translate-y-0.5 hover:bg-ink active:translate-y-1 active:shadow-none sm:w-auto">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Check Status
                    </button>
                </form>
            </section>

            @if($order)
                @php
                    $statusClass = match($order->status) {
                        'delivered' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
                        'waiting_delivery_charge' => 'bg-amber-50 text-amber-800 ring-amber-200',
                        default => 'bg-blue-50 text-blue-700 ring-blue-200',
                    };
                @endphp
                <section class="mt-5 rounded-lg border border-[#d8e3e0] bg-white p-5 shadow-sm sm:p-7" aria-live="polite">
                    <div class="flex flex-col gap-3 border-b border-gray-100 pb-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wide text-gray-400">Order Found</p>
                            <h2 class="mt-1 break-all text-xl font-black text-ink">{{ $order->order_number }}</h2>
                            <p class="mt-1 text-xs text-gray-500">Placed {{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <span class="inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-black capitalize ring-1 {{ $statusClass }}">{{ str($order->status)->replace('_', ' ') }}</span>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="rounded-md bg-[#f7f9f8] p-3">
                            <p class="text-[10px] font-black uppercase text-gray-400">Order Total</p>
                            <p class="mt-1 text-sm font-black text-ink">BDT {{ number_format($order->total, 2) }}</p>
                        </div>
                        <div class="rounded-md bg-[#f7f9f8] p-3">
                            <p class="text-[10px] font-black uppercase text-gray-400">Paid</p>
                            <p class="mt-1 text-sm font-black text-emerald-700">BDT {{ number_format($order->paidAmount(), 2) }}</p>
                        </div>
                        <div class="rounded-md bg-[#f7f9f8] p-3">
                            <p class="text-[10px] font-black uppercase text-gray-400">Due</p>
                            <p class="mt-1 text-sm font-black text-red-700">BDT {{ number_format($order->dueAmount(), 2) }}</p>
                        </div>
                        <div class="rounded-md bg-[#f7f9f8] p-3">
                            <p class="text-[10px] font-black uppercase text-gray-400">Courier</p>
                            <p class="mt-1 text-sm font-black capitalize text-ink">{{ $order->steadfast_status ? str($order->steadfast_status)->replace('_', ' ') : 'Not submitted' }}</p>
                        </div>
                    </div>

                    @if($order->steadfast_tracking_code || $order->parcel_id)
                        <div class="mt-4 flex items-center justify-between gap-3 rounded-md border border-[#cbded9] bg-[#edf6f3] px-4 py-3">
                            <span class="text-xs font-black text-gray-500">Tracking / Parcel ID</span>
                            <span class="break-all text-right font-mono text-sm font-black text-primary">{{ $order->steadfast_tracking_code ?: $order->parcel_id }}</span>
                        </div>
                    @endif

                    @if($order->hasAdjustment())
                        <div class="mt-4 flex items-start justify-between gap-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3">
                            <div>
                                <p class="text-xs font-black text-emerald-800">{{ $order->adjustmentLabel() ?: 'Order adjustment' }}</p>
                                <p class="mt-0.5 text-[11px] text-emerald-700">{{ $order->adjustment_reason }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-black {{ (float) $order->discount_amount > 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ (float) $order->discount_amount > 0 ? '-' : '+' }} BDT {{ number_format((float) $order->discount_amount > 0 ? $order->discount_amount : $order->extra_charge_amount, 2) }}
                            </span>
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </main>
</x-app-layout>
