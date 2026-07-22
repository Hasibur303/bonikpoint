<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');

        if (! in_array($status, ['pending', 'approved', 'all'], true)) {
            $status = 'pending';
        }

        return view('admin.reviews.index', [
            'reviews' => ProductReview::with('product', 'user', 'order')
                ->when($status === 'pending', fn ($query) => $query->where('is_approved', false))
                ->when($status === 'approved', fn ($query) => $query->where('is_approved', true))
                ->latest()
                ->paginate(15)
                ->withQueryString(),
            'status' => $status,
            'pendingCount' => ProductReview::where('is_approved', false)->count(),
        ]);
    }

    public function update(ProductReview $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved.');
    }

    public function destroy(ProductReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review removed.');
    }
}
