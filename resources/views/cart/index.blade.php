@section('title', 'Shopping Cart | Bonik Point')
@section('meta_description', 'Review the products in your Bonik Point shopping cart before checkout.')
@section('canonical', route('cart.index'))
@section('robots', 'noindex,follow')

<x-app-layout>
    <section class="container py-6 md:py-12">
        <h1 class="mb-5 text-2xl font-black uppercase text-ink md:mb-8 md:text-4xl">Shopping Cart</h1>
        @if(count($cartItems))
            <div class="grid gap-5 lg:grid-cols-[1fr_360px] lg:gap-8">
                <div class="space-y-3 md:space-y-4">
                    @foreach($cartItems as $item)
                        <div class="grid grid-cols-[72px_minmax(0,1fr)] gap-3 rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-100 md:grid-cols-[96px_minmax(0,1fr)_auto] md:items-center md:gap-4 md:p-4">
                            <img src="{{ $item['product']->image_url }}" alt="{{ $item['product']->name }}" width="96" height="96" loading="lazy" decoding="async" class="h-[72px] w-[72px] rounded-md object-cover md:h-24 md:w-24">
                            <div class="min-w-0">
                                <a href="{{ route('shop.show', $item['product']) }}" class="line-clamp-2 text-sm font-black leading-5 text-ink hover:text-primary md:text-base">{{ $item['product']->name }}</a>
                                @if($item['festival_title'])
                                    <p class="mt-1 text-xs font-bold uppercase tracking-wide text-accent">{{ $item['festival_title'] }}</p>
                                @endif
                                @if($item['product_color_name'])
                                    <p class="mt-1 flex items-center gap-2 text-xs font-semibold text-gray-500">
                                        <span class="h-3 w-3 rounded-full border border-black/10" style="background-color: {{ $item['product_color_hex'] ?: '#E5E7EB' }}"></span>
                                        Color: {{ $item['product_color_name'] }}
                                    </p>
                                @endif
                                <p class="mt-1 text-xs font-semibold text-gray-500 md:text-sm">BDT {{ number_format($item['unit_price'], 2) }} each</p>
                            </div>
                            <div class="col-span-2 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-3 md:col-span-1 md:flex-nowrap md:justify-end md:border-t-0 md:pt-0">
                                <div class="min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400 md:hidden">Item Total</p>
                                    <p class="text-sm font-black text-primary md:text-base">BDT {{ number_format($item['total'], 2) }}</p>
                                </div>
                                <div class="flex shrink-0 items-center gap-2">
                                    <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex items-center gap-1.5">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="cart_key" value="{{ $item['key'] }}">
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock }}" class="h-9 w-14 rounded-md border-gray-200 bg-[#f8faf9] text-center text-sm font-bold focus:border-primary focus:ring-primary md:w-20">
                                        <button class="h-9 rounded-md bg-gray-100 px-2.5 text-xs font-black text-ink hover:bg-gray-200 md:px-3 md:text-sm">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="cart_key" value="{{ $item['key'] }}">
                                        <button class="grid h-9 w-9 place-items-center rounded-md bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-700" title="Remove" aria-label="Remove {{ $item['product']->name }}">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <aside class="h-fit rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-100 md:p-6">
                    <h2 class="text-lg font-black text-ink md:text-xl">Order Summary</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between"><span>Subtotal</span><span>BDT {{ number_format(\App\Http\Controllers\CartController::subtotal(), 2) }}</span></div>
                        <div class="flex justify-between"><span>Shipping</span><span>BDT 0.00</span></div>
                        <div class="border-t pt-3 flex justify-between text-lg font-black text-ink"><span>Total</span><span>BDT {{ number_format(\App\Http\Controllers\CartController::subtotal(), 2) }}</span></div>
                    </div>
                    <a href="{{ route('checkout.start') }}" class="mt-6 block rounded-lg bg-primary px-5 py-3 text-center text-sm font-black text-white shadow-lg shadow-primary/20 hover:bg-ink md:px-6 md:text-base">Proceed to Checkout</a>
                </aside>
            </div>
        @else
            <div class="rounded-lg bg-white p-10 text-center shadow-sm ring-1 ring-gray-100">
                <p class="text-gray-500">Your cart is empty.</p>
                <a href="{{ route('shop.index') }}" class="mt-5 inline-block rounded-lg bg-primary px-6 py-3 font-semibold text-white hover:bg-ink">Start Shopping</a>
            </div>
        @endif
    </section>
</x-app-layout>
