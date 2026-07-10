<x-admin-layout>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-wide text-primary">Catalog / Main Category</p>
            <h1 class="text-4xl font-black text-ink">{{ $category->name }}</h1>
            <p class="mt-2 text-sm text-gray-500">{{ $category->products_count }} direct products in this main category</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.categories.index') }}" class="rounded border border-gray-200 px-5 py-2 font-semibold text-ink hover:border-primary hover:text-primary">Back</a>
            <a href="{{ route('admin.categories.edit', $category) }}" class="rounded border border-gray-200 px-5 py-2 font-semibold text-ink hover:border-primary hover:text-primary">Edit Main Category</a>
            <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" class="rounded bg-primary px-5 py-2 font-semibold text-white hover:bg-ink">Add Subcategory</a>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="p-4">Subcategory</th>
                    <th class="p-4">Products</th>
                    <th class="p-4">Status</th>
                    <th class="p-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($subcategories as $subcategory)
                    <tr>
                        <td class="p-4 font-semibold text-ink">{{ $subcategory->name }}</td>
                        <td class="p-4">{{ $subcategory->products_count }}</td>
                        <td class="p-4">{{ $subcategory->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.categories.edit', $subcategory) }}" class="font-semibold text-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $subcategory) }}" class="ml-3 inline" onsubmit="return confirm('Delete this subcategory?')">
                                @csrf
                                @method('DELETE')
                                <button class="font-semibold text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-8 text-center text-gray-500">No subcategories yet. Add one under {{ $category->name }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $subcategories->links() }}</div>
</x-admin-layout>
