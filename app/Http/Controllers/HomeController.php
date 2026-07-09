<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Festival;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
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
            ->latest()
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
        ]);
    }
}
