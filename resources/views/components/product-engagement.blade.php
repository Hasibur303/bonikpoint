@props(['product', 'compact' => false])

<div class="flex flex-wrap items-center gap-x-3 gap-y-1 {{ $compact ? 'mt-2 text-[10px] sm:text-xs' : 'my-4 text-sm' }} text-gray-500" data-product-engagement="{{ $product->id }}">
    <button type="button" data-product-like data-url="{{ route('products.like', $product) }}"
        aria-pressed="{{ $product->is_liked ? 'true' : 'false' }}" aria-label="Like {{ $product->name }}"
        title="Like product" class="inline-flex min-h-8 items-center gap-1.5 hover:text-red-600 disabled:opacity-50 {{ $product->is_liked ? 'text-red-600' : '' }}">
        <i class="{{ $product->is_liked ? 'fa-solid' : 'fa-regular' }} fa-heart" aria-hidden="true"></i>
        <span data-product-likes>{{ number_format($product->likes_count ?? 0) }}</span><span>likes</span>
    </button>
    <span class="inline-flex items-center gap-1" title="Unique browser sessions visiting this product">
        <i class="fa-regular fa-eye" aria-hidden="true"></i> {{ number_format($product->visits_count ?? 0) }} visits
    </span>
    <span class="inline-flex items-center gap-1" title="Quantity in delivered orders">
        <i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> {{ number_format($product->sold_quantity ?? 0) }} sold
    </span>
    <span data-like-error class="hidden w-full text-red-600" role="status"></span>
</div>
