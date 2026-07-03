<x-admin-layout>
    <div class="mb-6">
        <p class="text-sm font-bold uppercase tracking-wide text-primary">Catalog</p>
        <h1 class="text-4xl font-black text-ink">{{ $category->exists ? 'Edit Category' : 'Add Category' }}</h1>
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
                <select name="parent_id" class="w-full rounded border-gray-200">
                    <option value="">None - make this a main category</option>
                    @foreach($parentCategories as $parentCategory)
                        <option value="{{ $parentCategory->id }}" @selected(old('parent_id', $category->parent_id) == $parentCategory->id)>
                            {{ $parentCategory->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Choose a parent only when creating a sub category.</p>
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
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))> Active</label>
        </div>
        <button class="mt-6 rounded bg-primary px-6 py-3 font-semibold text-white">Save Category</button>
    </form>
</x-admin-layout>
