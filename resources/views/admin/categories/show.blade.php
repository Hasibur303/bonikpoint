<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 xl:flex-row xl:items-end">
        <div class="flex items-center gap-4">
            @if($category->image)
                <img src="{{ $category->image_url }}" alt="{{ $category->image_alt ?: $category->name }}" width="64" height="64" class="h-16 w-16 rounded-lg border border-gray-100 object-cover shadow-sm">
            @else
                <span class="grid h-16 w-16 shrink-0 place-items-center rounded-lg bg-[#e5f4f2] text-xl text-primary"><i class="fa-solid fa-layer-group"></i></span>
            @endif
            <div><p class="text-xs font-black uppercase tracking-wide text-primary">Main Category</p><h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">{{ $category->name }}</h1><p class="mt-1 text-sm text-gray-500">{{ $category->products_count }} direct products</p></div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex h-10 items-center gap-2 rounded-md border border-gray-200 bg-white px-4 text-xs font-black text-ink hover:border-primary hover:text-primary"><i class="fa-solid fa-arrow-left"></i>All Categories</a>
            <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex h-10 items-center gap-2 rounded-md border border-gray-200 bg-white px-4 text-xs font-black text-ink hover:border-primary hover:text-primary"><i class="fa-solid fa-pen"></i>Edit</a>
            <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" class="inline-flex h-10 items-center gap-2 rounded-md bg-primary px-4 text-xs font-black text-white hover:bg-ink"><i class="fa-solid fa-plus"></i>Add Subcategory</a>
        </div>
    </div>

    <section class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-[#e7edeb] px-5 py-4"><div><h2 class="text-base font-black text-ink">Subcategories</h2><p class="mt-0.5 text-xs text-gray-500">Products can be assigned to these catalog groups.</p></div><span class="rounded-full bg-[#edf5f3] px-2.5 py-1 text-[10px] font-black text-primary">{{ $subcategories->total() }} total</span></div>
        <div class="overflow-x-auto">
            <table class="min-w-[650px] w-full text-left text-sm"><thead><tr><th class="px-5 py-3">Subcategory</th><th class="px-4 py-3">Products</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead><tbody>
                @forelse($subcategories as $subcategory)
                    <tr><td class="px-5 py-4"><span class="font-black text-ink">{{ $subcategory->name }}</span><span class="mt-0.5 block text-[11px] text-gray-400">/{{ $subcategory->slug }}</span></td><td class="px-4 py-4 font-bold text-ink">{{ $subcategory->products_count }}</td><td class="px-4 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black ring-1 {{ $subcategory->is_active ? 'bg-emerald-50 text-emerald-700 ring-emerald-200' : 'bg-gray-100 text-gray-600 ring-gray-200' }}">{{ $subcategory->is_active ? 'Active' : 'Hidden' }}</span></td><td class="px-5 py-4"><div class="flex justify-end gap-1.5"><a href="{{ route('admin.categories.edit', $subcategory) }}" class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-500 hover:border-primary hover:text-primary" aria-label="Edit {{ $subcategory->name }}"><i class="fa-solid fa-pen text-[10px]"></i></a><form method="POST" action="{{ route('admin.categories.destroy', $subcategory) }}" onsubmit="return confirm('Delete this subcategory?')">@csrf @method('DELETE')<button class="grid h-8 w-8 place-items-center rounded-md border border-gray-200 text-gray-400 hover:border-red-200 hover:bg-red-50 hover:text-red-600" aria-label="Delete {{ $subcategory->name }}"><i class="fa-regular fa-trash-can text-xs"></i></button></form></div></td></tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-14 text-center"><span class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-gray-100 text-gray-400"><i class="fa-solid fa-folder-plus"></i></span><p class="mt-3 font-bold text-ink">No subcategories yet</p><a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" class="mt-2 inline-block text-xs font-black text-primary">Add the first subcategory</a></td></tr>
                @endforelse
            </tbody></table>
        </div>
    </section>
    <div class="mt-6">{{ $subcategories->links() }}</div>
</x-admin-layout>
