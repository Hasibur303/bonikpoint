<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUSES = [
        'waiting_delivery_charge',
        'pending',
        'confirmed',
        'processing',
        'delivered',
        'completed',
        'cancelled',
    ];

    public function index(Request $request): View
    {
        $status = $request->input('status');

        if (! in_array($status, self::STATUSES, true)) {
            $status = null;
        }

        return view('admin.orders.index', [
            'orders' => Order::with('user')
                ->when($status, fn ($query) => $query->where('status', $status))
                ->latest()
                ->paginate(12)
                ->withQueryString(),
            'selectedStatus' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items', 'user')]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:waiting_delivery_charge,pending,confirmed,processing,delivered,completed,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }
}
