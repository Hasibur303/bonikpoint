<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::with('category')->latest()->paginate(10),
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
        Product::create($this->validated($request));

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::with('parent')->orderBy('parent_id')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, $product));

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
            'description' => ['nullable', 'string'],
            'buying_price' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:120', 'unique:products,sku,'.($product?->id ?? 'NULL')],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = Str::slug($data['name']).($product?->exists ? '' : '-'.Str::random(5));
        $data['buying_price'] = $data['buying_price'] ?? 0;
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        return $data;
    }
}
