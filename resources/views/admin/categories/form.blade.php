<x-admin-layout>
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">Catalog</p>
        <h1 class="text-4xl font-black text-ink">
            @if($category->exists)
                Edit {{ $category->parent_id ? 'Subcategory' : 'Main Category' }}
            @elseif($parentCategory)
                Add Subcategory
            @else
                Add Main Category
            @endif
        </h1>
        @if($parentCategory)
            <p class="mt-2 text-sm font-semibold text-primary">Under: {{ $parentCategory->name }}</p>
        @endif
    </div>
    <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="max-w-3xl rounded-lg bg-white p-6 shadow-sm">
        @csrf
        @if($category->exists) @method('PUT') @endif
        <div class="space-y-5">
            <div>
                <label class="mb-1 block text-sm font-semibold">Name</label>
                <input name="name" value="{{ old('name', $category->name) }}" class="w-full rounded border-gray-200" required>
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Parent Category</label>
                @if($parentCategory && ! $category->exists)
                    <input type="hidden" name="parent_id" value="{{ $parentCategory->id }}">
                    <div class="rounded border border-gray-200 bg-gray-50 px-3 py-2 font-semibold text-ink">{{ $parentCategory->name }}</div>
                    <p class="mt-1 text-xs text-gray-500">This subcategory will be created under this main category.</p>
                @else
                    <select name="parent_id" class="w-full rounded border-gray-200">
                        <option value="">None - make this a main category</option>
                        @foreach($parentCategories as $mainCategory)
                            <option value="{{ $mainCategory->id }}" @selected(old('parent_id', $category->parent_id) == $mainCategory->id)>
                                {{ $mainCategory->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Leave empty for main category. Choose a parent only for subcategory.</p>
                @endif
                @error('parent_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Description</label>
                <textarea name="description" rows="4" class="w-full rounded border-gray-200">{{ old('description', $category->description) }}</textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Image</label>
                <input type="file" name="image" class="w-full rounded border border-gray-200 p-2">
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Image Alt Text</label>
                <input name="image_alt" value="{{ old('image_alt', $category->image_alt) }}" placeholder="Example: Vape pod device category thumbnail" class="w-full rounded border-gray-200">
                <p class="mt-1 text-xs text-gray-500">Shortly describe the category image for Google and accessibility.</p>
                @error('image_alt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="rounded-lg border border-primary/15 bg-primary/5 p-4">
                <p class="text-sm font-black uppercase tracking-wide text-primary">SEO Settings</p>
                <p class="mt-1 text-xs text-gray-600">Optional. Leave blank to use automatic SEO from category name and description.</p>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">SEO Title</label>
                        <input name="seo_title" value="{{ old('seo_title', $category->seo_title) }}" maxlength="255" placeholder="Example: Vape Pod Devices in Bangladesh | Bonik Point" class="w-full rounded border-gray-200">
                        <p class="mt-1 text-xs text-gray-500">Best around 50-60 characters.</p>
                        @error('seo_title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Meta Description</label>
                        <textarea name="seo_description" rows="3" maxlength="500" placeholder="Example: Browse rechargeable pod devices, vape pens, and accessories at Bonik Point with delivery across Bangladesh." class="w-full rounded border-gray-200">{{ old('seo_description', $category->seo_description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Best around 140-160 characters.</p>
                        @error('seo_description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))> Active</label>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <button class="rounded bg-primary px-6 py-3 font-semibold text-white">Save Category</button>
            @if($parentCategory)
                <a href="{{ route('admin.categories.show', $parentCategory) }}" class="rounded border border-gray-200 px-6 py-3 font-semibold text-ink hover:border-primary hover:text-primary">Cancel</a>
            @else
                <a href="{{ route('admin.categories.index') }}" class="rounded border border-gray-200 px-6 py-3 font-semibold text-ink hover:border-primary hover:text-primary">Cancel</a>
            @endif
        </div>
    </form>
</x-admin-layout>
