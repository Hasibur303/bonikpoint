<x-admin-layout>
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-xs font-black uppercase tracking-wide text-primary">Catalog</p>
            <h1 class="mt-1 text-3xl font-black text-ink sm:text-4xl">{{ $product->exists ? 'Edit Product' : 'Add Product' }}</h1>
            <p class="mt-2 text-sm text-gray-500">Manage product information, media, pricing, fulfillment, and SEO.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-[#d7e1df] bg-white px-4 text-xs font-black text-ink shadow-sm hover:border-primary hover:text-primary"><i class="fa-solid fa-arrow-left"></i>Back to Products</a>
    </div>
    <form id="product-editor-form" method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="rounded-lg border border-[#dfe7e5] bg-white p-5 shadow-sm sm:p-6">
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
                        <option value="{{ $category->id }}" data-vape="{{ str_contains(strtolower(($category->parent?->name ?? '').' '.$category->name), 'vape') ? '1' : '0' }}" @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->parent ? $category->parent->name.' / '.$category->name : $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="product-brand" class="mb-1 block text-sm font-semibold">Brand</label>
                <input id="product-brand" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="Example: SMOK, Rincoe, Samsung" class="w-full rounded border-gray-200">
                <p class="mt-1 text-xs text-gray-500">Optional. Adds correct brand information and a searchable brand page.</p>
                @error('brand')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div id="vape-options" class="hidden rounded-lg border border-primary/20 bg-primary/5 p-4 md:col-span-2">
                <div class="mb-4">
                    <p class="text-sm font-black text-primary">Vape Product Options</p>
                    <p class="mt-1 text-xs text-gray-600">Only shown for products in a Vape category. Add flavors only when customers need to choose one.</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Device Type</label>
                    <select name="vape_device_type" class="w-full rounded border-gray-200 md:max-w-md">
                        <option value="">Not applicable / no device type</option>
                        <option value="full_device" @selected(old('vape_device_type', $product->vape_device_type) === 'full_device')>Full Device</option>
                        <option value="cartridge_only" @selected(old('vape_device_type', $product->vape_device_type) === 'cartridge_only')>Cartridge Only</option>
                        <option value="battery_only" @selected(old('vape_device_type', $product->vape_device_type) === 'battery_only')>Battery Only</option>
                    </select>
                    @error('vape_device_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mt-5 border-t border-primary/15 pt-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <label class="block text-sm font-semibold">Flavors</label>
                            <p class="mt-1 text-xs text-gray-600">Examples: Mango Ice, Red Bull, Blueberry Raspberry. Customers choose a flavor before buying.</p>
                        </div>
                        <button type="button" id="add-flavor-row" class="rounded bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-primary">Add Flavor</button>
                    </div>

                    @if($product->exists && $product->flavors->isNotEmpty())
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            @foreach($product->flavors as $flavor)
                                <div class="rounded-md border border-primary/15 bg-white p-3">
                                    <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Flavor Name</label>
                                    <input name="existing_flavors[{{ $flavor->id }}][name]" value="{{ old('existing_flavors.'.$flavor->id.'.name', $flavor->name) }}" class="w-full rounded border-gray-200">
                                    <label class="mt-2 flex items-center gap-2 text-xs font-semibold text-red-600"><input type="checkbox" name="delete_flavors[]" value="{{ $flavor->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">Delete this flavor</label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @php($newFlavorRows = old('flavors', [['name' => '']]))
                    <div id="flavor-rows" class="mt-4 grid gap-3 md:grid-cols-2">
                        @foreach($newFlavorRows as $index => $flavor)
                            <div class="flavor-row rounded-md border border-dashed border-primary/30 bg-white p-3">
                                <label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Flavor</label>
                                <input name="flavors[{{ $index }}][name]" value="{{ $flavor['name'] ?? '' }}" placeholder="Example: Mango Ice" class="w-full rounded border-gray-200">
                            </div>
                        @endforeach
                    </div>
                    @error('existing_flavors.*.name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('flavors.*.name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
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
                <label class="mb-1 block text-sm font-semibold">Warranty / Guarantee</label>
                <select name="warranty_type" class="w-full rounded border-gray-200">
                    @foreach([
                        'none' => 'No Warranty',
                        'guarantee' => 'Guarantee',
                        'service_warranty' => 'Service Warranty',
                        'replacement_warranty' => 'Replacement Warranty',
                        'brand_warranty' => 'Brand Warranty',
                    ] as $value => $label)
                        <option value="{{ $value }}" @selected(old('warranty_type', $product->warranty_type ?? 'none') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('warranty_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Warranty Duration</label>
                <input name="warranty_duration" value="{{ old('warranty_duration', $product->warranty_duration) }}" placeholder="Example: 7 Days, 6 Months, 1 Year" class="w-full rounded border-gray-200">
                @error('warranty_duration')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="mb-1 block text-sm font-semibold">Warranty Details</label>
                <textarea name="warranty_details" rows="3" placeholder="Write what is covered, what is not covered, and how customer can claim." class="w-full rounded border-gray-200">{{ old('warranty_details', $product->warranty_details) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">This section will show on product details page only when warranty/guarantee is selected.</p>
                @error('warranty_details')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Main Image</label>
                <input type="file" name="image" accept="image/*" class="w-full rounded border border-gray-200 p-2">
                <p class="mt-1 text-xs text-gray-500">This image shows first on product cards and cart.</p>
                @if($product->exists && $product->image)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" width="96" height="96" loading="lazy" decoding="async" class="mt-3 h-24 w-24 rounded-md object-cover ring-1 ring-gray-100">
                @endif
                @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Image Alt Text</label>
                <input name="image_alt" value="{{ old('image_alt', $product->image_alt) }}" placeholder="Example: Rincoe Manto Nano A4 rechargeable pod device" class="w-full rounded border-gray-200">
                <p class="mt-1 text-xs text-gray-500">Shortly describe the main image for Google and screen readers.</p>
                @error('image_alt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-semibold">Gallery Images</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple class="w-full rounded border border-gray-200 p-2">
                <p class="mt-1 text-xs text-gray-500">Upload multiple extra photos for the product details page.</p>
                @error('gallery_images')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('gallery_images.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2 rounded-lg border border-primary/15 bg-primary/5 p-4">
                <div class="mb-4">
                    <p class="text-sm font-black uppercase tracking-wide text-primary">SEO Settings</p>
                    <p class="mt-1 text-xs text-gray-600">Optional. Leave blank to use automatic SEO from product name and description.</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold">SEO Title</label>
                        <input name="seo_title" value="{{ old('seo_title', $product->seo_title) }}" maxlength="255" placeholder="Example: Rincoe Manto Nano A4 Price in Bangladesh | Bonik Point" class="w-full rounded border-gray-200">
                        <p class="mt-1 text-xs text-gray-500">Best around 50-60 characters.</p>
                        @error('seo_title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold">Meta Description</label>
                        <textarea name="seo_description" rows="3" maxlength="500" placeholder="Example: Buy Rincoe Manto Nano A4 rechargeable pod system in Bangladesh with fast support from Bonik Point." class="w-full rounded border-gray-200">{{ old('seo_description', $product->seo_description) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Best around 140-160 characters.</p>
                        @error('seo_description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            @if($product->exists && $product->images->isNotEmpty())
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-semibold">Current Gallery</label>
                    <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-5">
                        @foreach($product->images as $image)
                            <label class="group rounded-lg border border-gray-100 bg-gray-50 p-2">
                                <img src="{{ $image->image_url }}" alt="{{ $product->name }}" width="240" height="240" loading="lazy" decoding="async" class="aspect-square w-full rounded-md object-cover ring-1 ring-gray-100">
                                <span class="mt-2 flex items-center gap-2 text-xs font-semibold text-red-600">
                                    <input type="checkbox" name="delete_gallery_images[]" value="{{ $image->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Delete image
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Tick images you want to remove, then save product.</p>
                </div>
            @endif
            <div class="md:col-span-2 rounded-lg border border-gray-100 bg-gray-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <label class="block text-sm font-semibold">Product Colors</label>
                        <p class="mt-1 text-xs text-gray-500">Add this only when customers need to choose a color before buying.</p>
                    </div>
                    <button type="button" id="add-color-row" class="rounded bg-ink px-4 py-2 text-sm font-semibold text-white hover:bg-primary">Add Color</button>
                </div>

                @if($product->exists && $product->colors->isNotEmpty())
                    <div class="mt-4 grid gap-3 md:grid-cols-2">
                        @foreach($product->colors as $color)
                            <div class="rounded-md border border-gray-200 bg-white p-3">
                                <div class="grid gap-3 sm:grid-cols-[1fr_130px]">
                                    <div>
                                        <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Color Name</label>
                                        <input name="existing_colors[{{ $color->id }}][name]" value="{{ old('existing_colors.'.$color->id.'.name', $color->name) }}" placeholder="Example: Black" class="w-full rounded border-gray-200">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Swatch</label>
                                        <input type="color" name="existing_colors[{{ $color->id }}][hex_code]" value="{{ old('existing_colors.'.$color->id.'.hex_code', $color->hex_code ?: '#087C7F') }}" class="h-10 w-full rounded border border-gray-200 bg-white p-1">
                                    </div>
                                </div>
                                <label class="mt-2 flex items-center gap-2 text-xs font-semibold text-red-600">
                                    <input type="checkbox" name="delete_colors[]" value="{{ $color->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    Delete this color
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                @php($newColorRows = old('colors', [['name' => '', 'hex_code' => '#087C7F']]))
                <div id="color-rows" class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach($newColorRows as $index => $color)
                        <div class="color-row rounded-md border border-dashed border-gray-300 bg-white p-3">
                            <div class="grid gap-3 sm:grid-cols-[1fr_130px]">
                                <div>
                                    <label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Color Name</label>
                                    <input name="colors[{{ $index }}][name]" value="{{ $color['name'] ?? '' }}" placeholder="Example: Black" class="w-full rounded border-gray-200">
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Swatch</label>
                                    <input type="color" name="colors[{{ $index }}][hex_code]" value="{{ $color['hex_code'] ?? '#087C7F' }}" class="h-10 w-full rounded border border-gray-200 bg-white p-1">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('existing_colors.*.name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('existing_colors.*.hex_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('colors.*.name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('colors.*.hex_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))> Active</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))> Featured</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="advance_delivery_charge" value="1" @checked(old('advance_delivery_charge', $product->exists ? $product->advance_delivery_charge : true))> Advance Delivery Charge</label>
            </div>
        </div>
        <div class="sticky bottom-4 z-10 mt-6 flex items-center justify-between gap-4 rounded-lg border border-[#d7e1df] bg-white/95 p-3 shadow-[0_12px_35px_rgba(16,63,68,0.13)] backdrop-blur">
            <p class="hidden pl-2 text-xs font-semibold text-gray-500 sm:block">Review pricing and stock before saving.</p>
            <button class="ml-auto inline-flex h-11 items-center gap-2 rounded-md bg-primary px-5 text-sm font-black text-white hover:bg-ink"><i class="fa-solid fa-floppy-disk"></i>Save Product</button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addButton = document.getElementById('add-color-row');
            const rows = document.getElementById('color-rows');
            const category = document.querySelector('select[name="category_id"]');
            const vapeOptions = document.getElementById('vape-options');
            const addFlavorButton = document.getElementById('add-flavor-row');
            const flavorRows = document.getElementById('flavor-rows');

            if (!addButton || !rows) {
                return;
            }

            addButton.addEventListener('click', function () {
                const index = rows.querySelectorAll('.color-row').length;
                const wrapper = document.createElement('div');
                wrapper.className = 'color-row rounded-md border border-dashed border-gray-300 bg-white p-3';
                wrapper.innerHTML = `
                    <div class="grid gap-3 sm:grid-cols-[1fr_130px]">
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Color Name</label>
                            <input name="colors[${index}][name]" placeholder="Example: Black" class="w-full rounded border-gray-200">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Swatch</label>
                            <input type="color" name="colors[${index}][hex_code]" value="#087C7F" class="h-10 w-full rounded border border-gray-200 bg-white p-1">
                        </div>
                    </div>
                `;
                rows.appendChild(wrapper);
            });

            const refreshVapeOptions = () => {
                const selected = category?.options[category.selectedIndex];
                vapeOptions?.classList.toggle('hidden', selected?.dataset.vape !== '1');
            };

            category?.addEventListener('change', refreshVapeOptions);
            refreshVapeOptions();

            addFlavorButton?.addEventListener('click', function () {
                const index = flavorRows.querySelectorAll('.flavor-row').length;
                const wrapper = document.createElement('div');
                wrapper.className = 'flavor-row rounded-md border border-dashed border-primary/30 bg-white p-3';
                wrapper.innerHTML = `<label class="mb-1 block text-xs font-bold uppercase text-gray-500">New Flavor</label><input name="flavors[${index}][name]" placeholder="Example: Mango Ice" class="w-full rounded border-gray-200">`;
                flavorRows.appendChild(wrapper);
            });
        });
    </script>
</x-admin-layout>
