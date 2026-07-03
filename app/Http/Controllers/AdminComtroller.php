<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class AdminComtroller extends Controller
{
    public function index()
    {
        return view('admin.index', [
            'ordersCount' => Order::count(),
            'productsCount' => Product::count(),
            'categoriesCount' => Category::count(),
            'usersCount' => User::where('utype', 'usr')->count(),
            'recentOrders' => Order::with('user')->latest()->take(6)->get(),
        ]);
    }
}
