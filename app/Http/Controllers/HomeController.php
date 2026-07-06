<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    
public function index()
    {
        return view('index', [
            'categories' => Category::where('is_active', true)->whereNull('parent_id')->withCount('products')->take(8)->get(),
            'spotlightProducts' => Product::with('category')
                ->withSum('orderItems as sold_quantity', 'quantity')
                ->where('is_active', true)
                ->orderByDesc('sold_quantity')
                ->latest()
                ->take(8)
                ->get(),
            'featuredProducts' => Product::with('category')->where('is_active', true)->where('is_featured', true)->take(8)->get(),
            'newProducts' => Product::with('category')->where('is_active', true)->latest()->take(8)->get(),
        ]);
    }


}
