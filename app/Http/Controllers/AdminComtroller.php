<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Http\Request;

class AdminComtroller extends Controller
{
    public function index()
    {
        $today = today();

        return view('admin.index', [
            'ordersCount' => Order::count(),
            'productsCount' => Product::count(),
            'categoriesCount' => Category::count(),
            'usersCount' => User::where('utype', 'usr')->count(),
            'pendingOrdersCount' => Order::whereIn('status', ['waiting_delivery_charge', 'pending'])->count(),
            'awaitingPaymentCount' => Order::where('status', 'waiting_delivery_charge')->count(),
            'lowStockCount' => Product::where('is_active', true)->where('stock', '<=', 5)->count(),
            'todayOrdersCount' => Order::whereDate('created_at', today())->count(),
            'recentOrders' => Order::with('user')->latest()->take(6)->get(),
            'visitorStats' => [
                'today' => SiteVisit::whereDate('visited_on', $today)->count(),
                'week' => SiteVisit::whereBetween('visited_on', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()])->distinct('visitor_hash')->count('visitor_hash'),
                'month' => SiteVisit::whereBetween('visited_on', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])->distinct('visitor_hash')->count('visitor_hash'),
                'total' => SiteVisit::distinct('visitor_hash')->count('visitor_hash'),
            ],
        ]);
    }
}
