<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->when($request->category, function ($query, $slug) {
                $category = Category::with('children')->where('slug', $slug)->first();

                if (! $category) {
                    return $query->whereRaw('1 = 0');
                }

                return $query->whereIn('category_id', $category->children->pluck('id')->push($category->id));
            })
            ->when($request->search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->whereNull('parent_id')->with('children')->orderBy('name')->get(),
            'selectedCategory' => $request->category,
            'search' => $request->search,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('shop.show', [
            'product' => $product->load('category'),
            'relatedProducts' => Product::where('is_active', true)
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->take(4)
                ->get(),
        ]);
    }
}
