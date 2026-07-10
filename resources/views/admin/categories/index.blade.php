<x-admin-layout>
    <div class="mb-6 flex items-center justify-between">
        <div><p class="text-sm font-bold uppercase tracking-wide text-primary">Catalog</p><h1 class="text-4xl font-black text-ink">Main Categories</h1></div>
        <a href="{{ route('admin.categories.create') }}" class="rounded bg-primary px-5 py-2 font-semibold text-white">Add Main Category</a>
    </div>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-4">Main Category</th><th class="p-4">Subcategories</th><th class="p-4">Products</th><th class="p-4">Status</th><th class="p-4"></th></tr></thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr>
                        <td class="p-4">
                            <a href="{{ route('admin.categories.show', $category) }}" class="font-semibold text-ink hover:text-primary">{{ $category->name }}</a>
                        </td>
                        <td class="p-4">{{ $category->children_count }}</td>
                        <td class="p-4">{{ $category->products_count }}</td>
                        <td class="p-4">{{ $category->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.categories.show', $category) }}" class="mr-3 font-semibold text-ink">Open</a>
                            <a href="{{ route('admin.categories.create', ['parent_id' => $category->id]) }}" class="mr-3 font-semibold text-accent">Add Subcategory</a>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="font-semibold text-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="ml-3 inline" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="font-semibold text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-500">No main categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $categories->links() }}</div>
</x-admin-layout>
