@props(['product'])

@php
    $discount = $product->compare_price && $product->compare_price > $product->price
        ? (int) round((1 - ($product->price / $product->compare_price)) * 100)
        : null;
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-md border border-[#d9e0de] bg-white shadow-[0_6px_20px_rgba(25,52,54,0.06)] transition duration-300 hover:-translate-y-1 hover:border-primary/35 hover:shadow-[0_16px_34px_rgba(18,59,62,0.13)] sm:rounded-lg">
    <a href="{{ route('shop.show', $product) }}" class="relative block aspect-square overflow-hidden bg-[#f8f9f7] p-1 sm:aspect-[4/3] sm:p-3">
        <img src="{{ $product->image_url }}" alt="{{ $product->image_alt ?: $product->name.' product image' }}" width="640" height="640" loading="lazy" decoding="async" class="h-full w-full object-contain transition duration-500 group-hover:scale-[1.04]">
        @if($discount)
            <span class="absolute left-1.5 top-1.5 rounded bg-[#f2b84b] px-1.5 py-0.5 text-[9px] font-black leading-none text-[#3b2b06] sm:left-3 sm:top-3 sm:rounded-md sm:px-2.5 sm:py-1 sm:text-xs">{{ $discount }}% OFF</span>
        @endif
        @if($product->is_featured)
            <span class="absolute right-1.5 top-1.5 hidden h-7 w-7 place-items-center rounded-md bg-white text-accent shadow-md sm:grid sm:right-3 sm:top-3 sm:h-8 sm:w-8" title="Featured product">
                <i class="fa-solid fa-star text-xs"></i>
            </span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-1.5 sm:p-3">
        <div class="hidden items-center justify-between gap-3 sm:flex">
            <p class="truncate text-[11px] font-bold uppercase tracking-wide text-primary">{{ $product->category?->name }}</p>
            <span class="flex shrink-0 items-center gap-1.5 text-[11px] font-semibold {{ $product->stock > 0 ? 'text-green-700' : 'text-red-600' }}">
                <span class="h-1.5 w-1.5 rounded-full {{ $product->stock > 0 ? 'bg-green-500' : 'bg-red-500' }}"></span>
                {{ $product->stock > 0 ? 'In stock' : 'Sold out' }}
            </span>
        </div>

        <a href="{{ route('shop.show', $product) }}" class="line-clamp-2 min-h-7 text-[10px] font-extrabold leading-[1.35] text-ink hover:text-primary sm:mt-2 sm:min-h-12 sm:text-base sm:font-black sm:leading-6">{{ $product->name }}</a>

        <div class="mt-auto flex items-end justify-between gap-1 pt-1.5 sm:gap-4 sm:pt-3">
            <div class="min-w-0">
                <span class="block text-[10px] font-black leading-tight text-primary sm:text-lg">BDT {{ number_format($product->price) }}</span>
                @if($product->compare_price)
                    <span class="mt-0.5 hidden text-xs font-medium text-gray-400 line-through sm:block">BDT {{ number_format($product->compare_price) }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('cart.store', $product) }}" class="js-add-to-cart">
                @csrf
                <button class="grid h-7 w-7 place-items-center rounded bg-ink text-[10px] text-white shadow-sm transition hover:bg-primary disabled:cursor-not-allowed disabled:bg-gray-300 sm:h-10 sm:w-10 sm:rounded-md sm:text-sm" title="Add to cart" aria-label="Add {{ $product->name }} to cart" @disabled($product->stock < 1)>
                    <i class="fa-solid fa-cart-plus"></i>
                </button>
            </form>
        </div>
    </div>
</article>
