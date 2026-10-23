@props(['product'])
@if(auth()->user()?->utype === 'adm' && session('storefront_edit_mode'))
    <button type="button" data-storefront-product-edit="{{ route('admin.storefront.products.edit', $product) }}"
        class="m-2 inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-primary bg-white px-3 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white"
        aria-label="Edit {{ $product->name }}"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Edit product</button>
@endif
