<?php

namespace App\Providers;

use App\Http\Controllers\CartController;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $view->with('headerCategories', Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->where('is_active', true)->orderBy('name')])
                ->orderBy('name')
                ->get())
                ->with('drawerCart', app(CartController::class)->snapshot());
        });

        View::composer('components.user-dashboard-layout', function ($view) {
            $view->with('unpaidDeliveryOrders', auth()->check()
                ? Order::where('user_id', auth()->id())
                    ->where('advance_delivery_required', true)
                    ->where('delivery_charge_payment_option', 'pay_later')
                    ->latest()
                    ->take(3)
                    ->get()
                : collect());
        });
    }
}
