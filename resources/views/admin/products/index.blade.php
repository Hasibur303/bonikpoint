<x-admin-layout>
    <div class="mb-6 flex items-center justify-between">
        <div><p class="text-sm font-bold uppercase tracking-wide text-primary">Catalog</p><h1 class="text-4xl font-black text-ink">Products</h1></div>
        <a href="{{ route('admin.products.create') }}" class="rounded bg-primary px-5 py-2 font-semibold text-white">Add Product</a>
    </div>
    <div class="overflow-hidden rounded-lg bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="p-4">Product</th><th class="p-4">Category</th><th class="p-4">Price</th><th class="p-4">Stock</th><th class="p-4">Status</th><th class="p-4"></th></tr></thead>
            <tbody class="divide-y">
                @forelse($products as $product)
                    <tr>
                        <td class="p-4 font-semibold text-ink">{{ $product->name }}</td>
                        <td class="p-4">{{ $product->category?->name }}</td>
                        <td class="p-4">৳{{ number_format($product->price, 2) }}</td>
                        <td class="p-4">{{ $product->stock }}</td>
                        <td class="p-4">{{ $product->is_active ? 'Active' : 'Hidden' }}</td>
                        <td class="p-4 text-right">
                            <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="ml-3 inline" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button class="font-semibold text-red-600">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-gray-500">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $products->links() }}</div>
</x-admin-layout>
