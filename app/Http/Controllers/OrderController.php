<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('orders.index', [
            'orders' => auth()->user()->orders()->latest()->paginate(10),
        ]);
    }

    public function show(Order $order): View
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('orders.show', ['order' => $order->load('items')]);
    }

    public function receipt(Order $order): View
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        return view('orders.receipt', ['order' => $order->load('items')]);
    }
}
