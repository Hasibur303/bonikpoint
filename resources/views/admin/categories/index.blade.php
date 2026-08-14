<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Catalog</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">Main Categories</h1>
            <p class="mt-2 text-sm text-gray-500">Organize the storefront and manage subcategories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex h-11 items-center justify-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white shadow-[0_8px_20px_rgba(8,124,127,0.16)] transition hover:bg-ink"><i class="fa-solid fa-plus text-xs"></i>Add Main Category</a>
    </div>

    <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="border-b border-[#e7edeb] px-5 py-3.5 text-xs font-bold text-gray-500">{{ $categories->total() }} main {{ str('category')->plural($categories->total()) }}</div>
        <div class="overflow-x-auto">
            <table class="min-w-[780px] w-full text-left text-sm">
                <thead><tr><th class="px-5 py-3">Category</th><th class="px-4 py-3">Subcategories</th><th class="px-4 py-3">Direct Products</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if($category->image)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->image_alt ?: $category->name }}" width="48" height="48" loading="lazy" decoding="async" class="h-12 w-12 rounded-md border border-gray-100 object-cover">
                                    @else
                                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-md bg-[#edf5f3] text-primary"><i class="fa-solid fa-layer-group"></i></span>
                                    @endif
                                    <div><a href="{{ route('admin.categories.show', $category) }}" class="font-black text-ink hover:text-primary">{{ $category->name }}</a><p class="mt-0.5 text-[11px] text-gray-400">/{{ $category->slug }}</p></div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5"><span class="font-black text-ink">{{ $category->children_count }}</span><span class="ml-1 text-xs text-gray-400">items</span></td>
                            <td class="px-4 py-3.5"><span class="font-black text-ink">{{ $category->products_count }}</span><span class="ml-1 text-xs text-gray-400">products</span></td>
                            <td class="px-4 py-3.5"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black ring-1 {{ $category->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-gray-100 text-gray-600 ring-gray-200' }}">{{ $category->is_active ? 'Active' : 'Hidden' }}</span></td>
                            <td class="px-5 py-3.5"><div class="flex items-center justify-end gap-1.5">
                                <div class="flex overflow-hidden rounded-md border border-gray-200">
                                    <form method="POST" action="{{ route('admin.categories.move', [$category, 'up']) }}">@csrf @method('PATCH')<button class="grid h-8 w-8 place-items-center text-gray-500 transition hover:bg-[#edf5f3] hover:text-primary" aria-label="Move {{ $category->name }} up" title="Move up"><i class="fa-solid fa-arrow-up text-[10px]"></i></button></form>
                                    <form method="POST" action="{{ route('admin.categories.move', [$category, 'down']) }}">@csrf @method('PATCH')<button class="grid h-8 w-8 place-items-center border-l border-gray-200 text-gray-500 transition hover:bg-[#edf5f3] hover:text-primary" aria-label="Move {{ $category->name }} down" title="Move down"><i class="fa-solid fa-arrow-down text-[10px]"></i></button></form>
                                </div>
                                <a href="{{ route('admin.categories.show', $category) }}" class="inline-flex h-8 items-center gap-1.5 rounded-md border border-gray-200 px-3 text-[11px] font-black text-ink transition hover:border-primary hover:text-primary"><i class="fa-solid fa-folder-open text-[10px]"></i>Open</a>
                                <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 transition hover:border-primary hover:text-primary" aria-label="Add subcategory under {{ $category->name }}" title="Add subcategory"><i class="fa-solid fa-plus text-[10px]"></i></a>
                                <a href="{{ route('admin.categories.edit', $category) }}" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 transition hover:border-primary hover:text-primary" aria-label="Edit {{ $category->name }}" title="Edit category"><i class="fa-solid fa-pen text-[10px]"></i></a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')<button class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600" aria-label="Delete {{ $category->name }}" title="Delete category"><i class="fa-regular fa-trash-can text-xs"></i></button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center text-sm text-gray-500">No main categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="mt-6">{{ $categories->links() }}</div>
</x-admin-layout>
