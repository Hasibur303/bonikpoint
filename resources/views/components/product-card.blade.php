@props(['product'])

<div class="group overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-1 hover:shadow-lg">
    <a href="{{ route('shop.show', $product) }}" class="block aspect-square overflow-hidden bg-gray-100">
        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
    </a>
    <div class="p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-primary">{{ $product->category?->name }}</p>
        <a href="{{ route('shop.show', $product) }}" class="mt-1 block font-semibold text-ink hover:text-primary">{{ $product->name }}</a>
        <div class="mt-2 flex items-center justify-between gap-3">
            <div>
                <span class="font-bold text-primary">৳{{ number_format($product->price, 2) }}</span>
                @if($product->compare_price)
                    <span class="ml-2 text-sm text-gray-400 line-through">৳{{ number_format($product->compare_price, 2) }}</span>
                @endif
            </div>
            <form method="POST" action="{{ route('cart.store', $product) }}">
                @csrf
                <button class="grid h-9 w-9 place-items-center rounded-full bg-accent text-white hover:bg-primary" title="Add to cart">
                    <i class="fa-solid fa-plus"></i>
                </button>
            </form>
        </div>
    </div>
</div>
