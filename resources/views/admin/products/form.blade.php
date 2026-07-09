<x-admin-layout>
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">Catalog</p>
        <h1 class="text-4xl font-black text-ink">{{ $product->exists ? 'Edit Product' : 'Add Product' }}</h1>
    </div>
    <form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @if($product->exists) @method('PUT') @endif
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold">Name</label>
                <input name="name" value="{{ old('name', $product->name) }}" class="w-full rounded border-gray-200" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Category</label>
                <select name="category_id" class="w-full rounded border-gray-200" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->parent ? $category->parent->name.' / '.$category->name : $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Price</label>
                <input name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" class="w-full rounded border-gray-200" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Buying Price</label>
                <input name="buying_price" type="number" step="0.01" value="{{ old('buying_price', $product->buying_price ?? 0) }}" class="w-full rounded border-gray-200">
                <p class="mt-1 text-xs text-gray-500">Admin only. Used for profit calculation and hidden from shop pages.</p>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Compare Price</label>
                <input name="compare_price" type="number" step="0.01" value="{{ old('compare_price', $product->compare_price) }}" class="w-full rounded border-gray-200">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Stock</label>
                <input name="stock" type="number" value="{{ old('stock', $product->stock ?? 0) }}" class="w-full rounded border-gray-200" required>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">SKU</label>
                <input name="sku" value="{{ old('sku', $product->sku) }}" class="w-full rounded border-gray-200">
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold">Description</label>
                <textarea name="description" rows="5" class="w-full rounded border-gray-200">{{ old('description', $product->description) }}</textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Image</label>
                <input type="file" name="image" class="w-full rounded border border-gray-200 p-2">
            </div>
            <div class="flex items-center gap-6">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))> Active</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Featured</label>
            </div>
        </div>
        <button class="mt-6 rounded bg-primary px-6 py-3 font-semibold text-white">Save Product</button>
    </form>
</x-admin-layout>
