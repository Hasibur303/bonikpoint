<x-admin-layout>
    @php
        $selectedCategoryValues = collect(old('categories', $selectedCategories))->map(fn ($id) => (int) $id)->all();
        $selectedProductValues = collect(old('products', $selectedProducts))->map(fn ($id) => (int) $id)->all();
    @endphp

    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">Marketing</p>
        <h1 class="text-4xl font-black text-ink">{{ $festival->exists ? 'Edit Festival Offer' : 'Add Festival Offer' }}</h1>
    </div>

    <form method="POST" action="{{ $festival->exists ? route('admin.festivals.update', $festival) : route('admin.festivals.store') }}" enctype="multipart/form-data" class="rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @if($festival->exists) @method('PUT') @endif

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold">Festival Title</label>
                <input name="title" value="{{ old('title', $festival->title) }}" class="w-full rounded border-gray-200" required>
                @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Discount Percentage</label>
                <input name="discount_percentage" type="number" step="0.01" min="0" max="100" value="{{ old('discount_percentage', $festival->discount_percentage ?? 0) }}" class="w-full rounded border-gray-200" required>
                @error('discount_percentage')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Start Date</label>
                <input name="starts_at" type="date" value="{{ old('starts_at', $festival->starts_at?->format('Y-m-d')) }}" class="w-full rounded border-gray-200">
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">End Date</label>
                <input name="ends_at" type="date" value="{{ old('ends_at', $festival->ends_at?->format('Y-m-d')) }}" class="w-full rounded border-gray-200">
                @error('ends_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold">Banner / Photo</label>
                <input type="file" name="banner" class="w-full rounded border border-gray-200 p-2">
                @if($festival->banner)
                    <img src="{{ $festival->banner_url }}" alt="{{ $festival->title }}" class="mt-3 h-36 rounded object-cover">
                @endif
            </div>

            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold">Description</label>
                <textarea name="description" rows="4" class="w-full rounded border-gray-200">{{ old('description', $festival->description) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold">Select Categories / Subcategories For This Festival</label>
                <p class="mb-3 text-sm text-gray-500">Selecting a main category includes products from that category and its subcategories. You can also select only a specific subcategory.</p>
                <div class="grid max-h-80 gap-2 overflow-y-auto rounded border border-gray-100 p-3 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($categories as $category)
                        <div class="rounded border border-gray-100 p-3">
                            <label class="flex items-start gap-2">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $selectedCategoryValues, true)) class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                                <span class="font-semibold text-ink">{{ $category->name }}</span>
                            </label>

                            @if($category->children->isNotEmpty())
                                <div class="mt-2 grid gap-1 border-t border-gray-100 pt-2">
                                    @foreach($category->children->sortBy('name') as $child)
                                        <label class="flex items-start gap-2 rounded px-2 py-1 hover:bg-gray-50">
                                            <input type="checkbox" name="categories[]" value="{{ $child->id }}" @checked(in_array($child->id, $selectedCategoryValues, true)) class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                                            <span class="text-sm text-gray-700">{{ $child->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                @error('categories')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('categories.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-semibold">Select Extra Individual Products</label>
                <p class="mb-3 text-sm text-gray-500">Optional. Use this when you want to add a few products outside the selected categories.</p>
                <div class="grid max-h-96 gap-2 overflow-y-auto rounded border border-gray-100 p-3 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($products as $product)
                        <label class="flex items-start gap-2 rounded p-2 hover:bg-gray-50">
                            <input type="checkbox" name="products[]" value="{{ $product->id }}" @checked(in_array($product->id, $selectedProductValues, true)) class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                            <span>
                                <span class="block font-semibold text-ink">{{ $product->name }}</span>
                                <span class="text-xs text-gray-500">{{ $product->category?->name }} - BDT {{ number_format($product->price, 2) }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $festival->exists ? $festival->is_active : true))>
                Active
            </label>
        </div>

        <button class="mt-6 rounded bg-primary px-6 py-3 font-semibold text-white">Save Festival</button>
    </form>
</x-admin-layout>
