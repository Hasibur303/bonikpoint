<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\ImageUploadOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::whereNull('parent_id')
                ->withCount(['children', 'products'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function create(Request $request): View
    {
        $parentCategory = null;

        if ($request->filled('parent_id')) {
            $parentCategory = Category::whereNull('parent_id')->findOrFail($request->integer('parent_id'));
        }

        return view('admin.categories.form', [
            'category' => new Category,
            'parentCategories' => Category::whereNull('parent_id')->orderBy('sort_order')->orderBy('name')->get(),
            'parentCategory' => $parentCategory,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['sort_order'] = ((int) Category::where('parent_id', $data['parent_id'])->max('sort_order')) + 1;
        $category = Category::create($data);

        if ($category->parent_id) {
            return redirect()->route('admin.categories.show', $category->parent_id)->with('success', 'Subcategory created.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function show(Category $category): View
    {
        abort_if($category->parent_id, 404);

        return view('admin.categories.show', [
            'category' => $category->loadCount('products'),
            'subcategories' => $category->children()
                ->withCount('products')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(12),
        ]);
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.form', [
            'category' => $category,
            'parentCategories' => Category::whereNull('parent_id')
                ->whereKeyNot($category->id)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'parentCategory' => $category->parent,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $data = $this->validated($request, $category);

        if ($category->parent_id != $data['parent_id']) {
            $data['sort_order'] = ((int) Category::where('parent_id', $data['parent_id'])->max('sort_order')) + 1;
        }

        $category->update($data);

        if ($category->parent_id) {
            return redirect()->route('admin.categories.show', $category->parent_id)->with('success', 'Subcategory updated.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->withErrors([
                'category' => 'Delete or move this category subcategories before deleting the main category.',
            ]);
        }

        if ($category->products()->exists()) {
            return back()->withErrors([
                'category' => 'Move or delete this category products before deleting the category.',
            ]);
        }

        $parentId = $category->parent_id;
        $category->delete();

        if ($parentId) {
            return redirect()->route('admin.categories.show', $parentId)->with('success', 'Subcategory deleted.');
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    public function move(Category $category, string $direction): RedirectResponse
    {
        abort_unless(in_array($direction, ['up', 'down'], true), 404);

        $siblings = Category::where('parent_id', $category->parent_id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $currentIndex = $siblings->search(fn (Category $sibling) => $sibling->is($category));
        $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

        if ($currentIndex === false || ! $siblings->has($targetIndex)) {
            return back();
        }

        $target = $siblings->get($targetIndex);

        DB::transaction(function () use ($category, $target) {
            [$categoryOrder, $targetOrder] = [$category->sort_order, $target->sort_order];
            $category->update(['sort_order' => $targetOrder]);
            $target->update(['sort_order' => $categoryOrder]);
        });

        return back()->with('success', 'Category order updated.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                Rule::exists('categories', 'id')->whereNull('parent_id'),
            ],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($category?->exists && $category->children()->exists() && filled($data['parent_id'] ?? null)) {
            throw ValidationException::withMessages([
                'parent_id' => 'A main category with subcategories cannot be converted into a subcategory.',
            ]);
        }

        $data['slug'] = Str::slug($data['name']).($category?->exists ? '' : '-'.Str::lower(Str::random(5)));
        $data['parent_id'] = $data['parent_id'] ?? null;
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = ImageUploadOptimizer::store($request->file('image'), 'categories');
        }

        return $data;
    }
}
