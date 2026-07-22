<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Catalog</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Products</h1>
            <p class="mt-2 text-sm text-gray-500">Manage pricing, stock, product details, and visibility.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white shadow-[0_8px_20px_rgba(8,124,127,0.16)] transition hover:bg-ink">
            <i class="fa-solid fa-plus text-xs"></i>
            Add Product
        </a>
    </div>

    <form method="GET" action="{{ route('admin.products.index') }}" class="mb-5 grid gap-3 rounded-lg border border-[#dfe7e5] bg-white p-4 shadow-sm md:grid-cols-[minmax(220px,1fr)_220px_180px_auto]">
        <label class="relative">
            <span class="sr-only">Search products</span>
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
            <input name="search" value="{{ $search }}" placeholder="Search name, SKU, or brand" class="h-11 w-full pl-10 text-sm">
        </label>
        <label>
            <span class="sr-only">Filter by category</span>
            <select name="category" class="h-11 w-full text-sm">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected($selectedCategory === $category->id)>{{ $category->parent ? $category->parent->name.' / '.$category->name : $category->name }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span class="sr-only">Filter by status</span>
            <select name="status" class="h-11 w-full text-sm">
                <option value="">All statuses</option>
                <option value="active" @selected($selectedStatus === 'active')>Active</option>
                <option value="hidden" @selected($selectedStatus === 'hidden')>Hidden</option>
                <option value="low_stock" @selected($selectedStatus === 'low_stock')>Low stock</option>
            </select>
        </label>
        <div class="flex gap-2">
            <button class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-md bg-ink px-5 text-xs font-black text-white transition hover:bg-primary"><i class="fa-solid fa-filter"></i>Apply</button>
            @if($search || $selectedCategory || $selectedStatus)
                <a href="{{ route('admin.products.index') }}" class="grid h-11 w-11 shrink-0 place-items-center rounded-md border border-gray-200 bg-white text-gray-500 transition hover:border-red-200 hover:text-red-600" aria-label="Clear product filters" title="Clear filters"><i class="fa-solid fa-xmark"></i></a>
            @endif
        </div>
    </form>

    <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#e7edeb] px-5 py-3.5">
            <p class="text-xs font-bold text-gray-500">{{ $products->total() }} {{ str('product')->plural($products->total()) }}</p>
            <p class="text-[11px] font-semibold text-gray-400">Low stock: 5 units or fewer</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-[1040px] w-full text-left text-sm">
                <thead><tr><th class="px-5 py-3">Product</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Selling Price</th><th class="px-4 py-3">Cost</th><th class="px-4 py-3">Stock</th><th class="px-4 py-3">Delivery</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->image_alt ?: $product->name }}" width="48" height="48" loading="lazy" decoding="async" class="h-12 w-12 rounded-md border border-gray-100 bg-gray-50 object-cover">
                                    <div class="min-w-0 max-w-[270px]">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="block truncate font-black text-ink hover:text-primary">{{ $product->name }}</a>
                                        <p class="mt-0.5 flex items-center gap-2 text-[11px] text-gray-400"><span>{{ $product->sku ?: 'No SKU' }}</span>@if($product->faqs_count)<span class="h-1 w-1 rounded-full bg-gray-300"></span><span>{{ $product->faqs_count }} FAQ</span>@endif</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5"><span class="inline-flex rounded-md bg-[#f2f6f5] px-2.5 py-1 text-xs font-bold text-gray-600">{{ $product->category?->name ?: 'Uncategorized' }}</span></td>
                            <td class="px-4 py-3.5"><span class="font-black text-ink">BDT {{ number_format($product->price, 2) }}</span>@if($product->compare_price)<span class="mt-0.5 block text-[11px] text-gray-400 line-through">BDT {{ number_format($product->compare_price, 2) }}</span>@endif</td>
                            <td class="px-4 py-3.5 text-xs font-semibold text-gray-600">BDT {{ number_format($product->buying_price ?? 0, 2) }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-black ring-1 {{ $product->stock <= 0 ? 'bg-red-50 text-red-700 ring-red-200' : ($product->stock <= 5 ? 'bg-amber-50 text-amber-800 ring-amber-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200') }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $product->stock }} units
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-xs font-bold {{ $product->advance_delivery_charge ? 'text-primary' : 'text-gray-400' }}">{{ $product->advance_delivery_charge ? 'Advance' : 'Pay later' }}</td>
                            <td class="px-4 py-3.5"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black ring-1 {{ $product->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-gray-100 text-gray-600 ring-gray-200' }}">{{ $product->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('shop.show', $product) }}" target="_blank" rel="noopener noreferrer" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 transition hover:border-primary hover:text-primary" aria-label="View {{ $product->name }}" title="View product"><i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i></a>
                                    <a href="{{ route('admin.products.faqs.edit', $product) }}" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 transition hover:border-primary hover:text-primary" aria-label="Manage FAQ for {{ $product->name }}" title="Manage FAQ"><i class="fa-regular fa-circle-question text-xs"></i></a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 transition hover:border-primary hover:text-primary" aria-label="Edit {{ $product->name }}" title="Edit product"><i class="fa-solid fa-pen text-[10px]"></i></a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                        @csrf @method('DELETE')
                                        <button class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600" aria-label="Delete {{ $product->name }}" title="Delete product"><i class="fa-regular fa-trash-can text-xs"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-14 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-gray-100 text-gray-400"><i class="fa-solid fa-box-open"></i></span><p class="mt-3 font-bold text-ink">No products found</p><p class="mt-1 text-xs text-gray-500">Try changing the filters or add a new product.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="mt-6">{{ $products->links() }}</div>
</x-admin-layout>
