<?php

namespace App\Http\Controllers\Admin;

use App\Support\ImageUploadOptimizer;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::with('category')->withCount('faqs')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(),
            'categories' => Category::with('parent')->orderBy('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->validated($request));
        $this->storeGalleryImages($request, $product);
        $this->storeNewColors($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product->load('images', 'colors'),
            'categories' => Category::with('parent')->orderBy('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, $product));
        $this->deleteSelectedGalleryImages($request, $product);
        $this->storeGalleryImages($request, $product);
        $this->deleteSelectedColors($request, $product);
        $this->updateExistingColors($request, $product);
        $this->storeNewColors($request, $product);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'buying_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:120', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:4096'],
            'delete_gallery_images' => ['nullable', 'array'],
            'delete_gallery_images.*' => ['integer', 'exists:product_images,id'],
            'existing_colors' => ['nullable', 'array'],
            'existing_colors.*.name' => ['nullable', 'string', 'max:80'],
            'existing_colors.*.hex_code' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'colors' => ['nullable', 'array'],
            'colors.*.name' => ['nullable', 'string', 'max:80'],
            'colors.*.hex_code' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'delete_colors' => ['nullable', 'array'],
            'delete_colors.*' => ['integer', 'exists:product_colors,id'],
            'advance_delivery_charge' => ['nullable', 'boolean'],
            'warranty_type' => ['required', 'in:none,guarantee,service_warranty,replacement_warranty,brand_warranty'],
            'warranty_duration' => ['nullable', 'string', 'max:120'],
            'warranty_details' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['name']).($product?->exists ? '' : '-'.Str::lower(Str::random(5)));
        $data['buying_price'] = $data['buying_price'] ?? 0;
        $data['advance_delivery_charge'] = $request->boolean('advance_delivery_charge');
        $data['warranty_duration'] = $data['warranty_type'] === 'none' ? null : $data['warranty_duration'];
        $data['warranty_details'] = $data['warranty_type'] === 'none' ? null : $data['warranty_details'];
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = ImageUploadOptimizer::store($request->file('image'), 'products');
        }

        unset($data['gallery_images'], $data['delete_gallery_images'], $data['existing_colors'], $data['colors'], $data['delete_colors']);

        return $data;
    }

    private function storeGalleryImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('gallery_images')) {
            return;
        }

        $nextSortOrder = ((int) $product->images()->max('sort_order')) + 1;

        foreach ($request->file('gallery_images') as $image) {
            $product->images()->create([
                'image' => ImageUploadOptimizer::store($image, 'products/gallery'),
                'sort_order' => $nextSortOrder++,
            ]);
        }
    }

    private function deleteSelectedGalleryImages(Request $request, Product $product): void
    {
        $imageIds = collect($request->input('delete_gallery_images', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        if ($imageIds === []) {
            return;
        }

        ProductImage::where('product_id', $product->id)
            ->whereIn('id', $imageIds)
            ->get()
            ->each(function (ProductImage $image): void {
                Storage::disk('public')->delete($image->image);
                $image->delete();
            });
    }

    private function storeNewColors(Request $request, Product $product): void
    {
        $colors = collect($request->input('colors', []))
            ->map(fn ($color) => [
                'name' => trim((string) ($color['name'] ?? '')),
                'hex_code' => $this->normalizeHexCode($color['hex_code'] ?? null),
            ])
            ->filter(fn ($color) => $color['name'] !== '')
            ->values();

        if ($colors->isEmpty()) {
            return;
        }

        $nextSortOrder = ((int) $product->colors()->max('sort_order')) + 1;

        $colors->each(function (array $color) use ($product, &$nextSortOrder): void {
            $product->colors()->create([
                'name' => $color['name'],
                'hex_code' => $color['hex_code'],
                'sort_order' => $nextSortOrder++,
            ]);
        });
    }

    private function updateExistingColors(Request $request, Product $product): void
    {
        collect($request->input('existing_colors', []))
            ->each(function (array $color, int|string $id) use ($product): void {
                $name = trim((string) ($color['name'] ?? ''));
                $productColor = ProductColor::where('product_id', $product->id)->whereKey($id)->first();

                if (! $productColor) {
                    return;
                }

                if ($name === '') {
                    $productColor->delete();
                    return;
                }

                $productColor->update([
                    'name' => $name,
                    'hex_code' => $this->normalizeHexCode($color['hex_code'] ?? null),
                ]);
            });
    }

    private function deleteSelectedColors(Request $request, Product $product): void
    {
        $colorIds = collect($request->input('delete_colors', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->all();

        if ($colorIds === []) {
            return;
        }

        ProductColor::where('product_id', $product->id)
            ->whereIn('id', $colorIds)
            ->delete();
    }

    private function normalizeHexCode(?string $hexCode): ?string
    {
        $hexCode = trim((string) $hexCode);

        return $hexCode === '' ? null : strtoupper($hexCode);
    }
}
