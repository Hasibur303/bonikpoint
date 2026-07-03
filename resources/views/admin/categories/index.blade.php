<x-admin-layout>
    <div class="mb-6 flex items-center justify-between">
        <div><p class="text-sm font-bold uppercase tracking-wide text-primary">Catalog</p><h1 class="text-4xl font-black text-ink">Categories</h1></div>
        <a href="{{ route('admin.categories.create') }}" class="rounded bg-primary px-5 py-2 font-semibold text-white">Add Category / Sub Category</a>
    </div>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-4">Name</th><th class="p-4">Type</th><th class="p-4">Parent</th><th class="p-4">Status</th><th class="p-4">Products</th><th class="p-4"></th></tr></thead>
            <tbody class="divide-y">
                @forelse($categories as $category)
                    <tr>
                        <td class="p-4 font-semibold text-ink">{{ $category->name }}</td>
                        <td class="p-4">{{ $category->parent_id ? 'Sub Category' : 'Main Category' }}</td>
                        <td class="p-4">{{ $category->parent?->name ?? '-' }}</td>
                        <td class="p-4">{{ $category->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="p-4">{{ $category->products()->count() }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="font-semibold text-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="ml-3 inline" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="font-semibold text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $categories->links() }}</div>
</x-admin-layout>
