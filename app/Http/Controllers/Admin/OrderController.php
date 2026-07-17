<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    private const STATUSES = [
        'waiting_delivery_charge',
        'pending',
        'confirmed',
        'processing',
        'delivered',
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

    public function receipt(Order $order): View
    {
        return view('orders.receipt', [
            'order' => $order->load('items'),
            'adminReceipt' => true,
        ]);
    }

    public function paymentProof(Order $order): StreamedResponse
    {
        abort_unless($order->delivery_payment_proof && Storage::disk('local')->exists($order->delivery_payment_proof), 404);

        return Storage::disk('local')->response(
            $order->delivery_payment_proof,
            'payment-proof-'.$order->order_number.'.'.pathinfo($order->delivery_payment_proof, PATHINFO_EXTENSION),
            ['Cache-Control' => 'private, no-store']
        );
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:waiting_delivery_charge,pending,confirmed,processing,delivered,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }
}
