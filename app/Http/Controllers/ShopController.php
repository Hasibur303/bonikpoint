<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Festival;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $today = today()->toDateString();

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
            ->when($request->min_price, fn ($query, $price) => $query->where('price', '>=', (float) $price))
            ->when($request->max_price, fn ($query, $price) => $query->where('price', '<=', (float) $price))
            ->when($request->sort === 'price_low', fn ($query) => $query->orderBy('price'))
            ->when($request->sort === 'price_high', fn ($query) => $query->orderByDesc('price'))
            ->when(! in_array($request->sort, ['price_low', 'price_high'], true), fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', [
            'products' => $products,
            'festivals' => Festival::where('is_active', true)
                ->where(fn ($query) => $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', $today))
                ->where(fn ($query) => $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', $today))
                ->withCount('products')
                ->latest()
                ->take(6)
                ->get(),
            'categories' => Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
                ->orderBy('name')
                ->get(),
            'selectedCategory' => $request->category,
            'search' => $request->search,
            'minPrice' => $request->min_price,
            'maxPrice' => $request->max_price,
            'sort' => $request->sort,
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        return view('shop.show', [
            'product' => $product->load('category.parent')
                ->loadCount(['reviews' => fn ($query) => $query->where('is_approved', true)])
                ->loadAvg(['reviews' => fn ($query) => $query->where('is_approved', true)], 'rating'),
            'reviews' => $product->reviews()
                ->with('user')
                ->where('is_approved', true)
                ->latest()
                ->take(12)
                ->get(),
            'relatedProducts' => Product::where('is_active', true)
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->take(4)
                ->get(),
        ]);
    }
}
