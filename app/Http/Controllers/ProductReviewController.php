<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless($order->status === 'delivered', 403);

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless($order->items()->where('product_id', $data['product_id'])->exists(), 403);

        ProductReview::updateOrCreate(
            [
                'product_id' => $data['product_id'],
                'user_id' => auth()->id(),
                'order_id' => $order->id,
            ],
            [
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'is_approved' => false,
            ]
        );

        return back()->with('success', 'Thank you. Your product review has been submitted for approval.');
    }
}
