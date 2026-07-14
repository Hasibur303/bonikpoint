<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Festival;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FestivalController extends Controller
{
    public function index(): View
    {
        return view('admin.festivals.index', [
            'festivals' => Festival::withCount(['products', 'categories'])->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.festivals.form', [
            'festival' => new Festival(),
            'categories' => Category::with('children')->whereNull('parent_id')->orderBy('name')->get(),
            'products' => Product::with('category')->where('is_active', true)->orderBy('name')->get(),
            'selectedCategories' => [],
            'selectedProducts' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $festival = Festival::create($this->validated($request));
        $festival->categories()->sync($request->input('categories', []));
        $festival->products()->sync($request->input('products', []));

        return redirect()->route('admin.festivals.index')->with('success', 'Festival offer created.');
    }

    public function edit(Festival $festival): View
    {
        return view('admin.festivals.form', [
            'festival' => $festival,
            'categories' => Category::with('children')->whereNull('parent_id')->orderBy('name')->get(),
            'products' => Product::with('category')->where('is_active', true)->orderBy('name')->get(),
            'selectedCategories' => $festival->categories()->pluck('categories.id')->all(),
            'selectedProducts' => $festival->products()->pluck('products.id')->all(),
        ]);
    }

    public function update(Request $request, Festival $festival): RedirectResponse
    {
        $festival->update($this->validated($request, $festival));
        $festival->categories()->sync($request->input('categories', []));
        $festival->products()->sync($request->input('products', []));

        return redirect()->route('admin.festivals.index')->with('success', 'Festival offer updated.');
    }

    public function destroy(Festival $festival): RedirectResponse
    {
        $festival->delete();

        return redirect()->route('admin.festivals.index')->with('success', 'Festival offer deleted.');
    }

    private function validated(Request $request, ?Festival $festival = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
        ]);

        unset($data['products']);
        unset($data['categories']);

        $data['slug'] = $festival?->exists ? $festival->slug : Str::slug($data['title']).'-'.Str::lower(Str::random(5));
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('banner')) {
            $data['banner'] = $request->file('banner')->store('festivals', 'public');
        }

        return $data;
    }
}
