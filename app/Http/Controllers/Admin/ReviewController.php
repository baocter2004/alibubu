<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = ProductReview::query()
            ->with(['product:id,name,slug,thumbnail', 'user:id,fullname,email'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_approved', $request->input('status') === 'approved');
            })
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->input('keyword');
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('title', 'like', "%{$keyword}%")
                        ->orWhere('comment', 'like', "%{$keyword}%")
                        ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->latest('id')
            ->paginate(15);

        return view('admin.pages.reviews.index', [
            'reviews' => $reviews,
            'pending' => ProductReview::where('is_approved', false)->count(),
            'approved' => ProductReview::where('is_approved', true)->count(),
        ]);
    }

    public function approve(int|string $id)
    {
        $review = ProductReview::findOrFail($id);

        $review->update(['is_approved' => true, 'approved_at' => now()]);
        $review->product->refreshRating();

        return back()->with('success', __('admin/review.messages.approved'));
    }

    public function reject(int|string $id)
    {
        $review = ProductReview::findOrFail($id);

        $review->update(['is_approved' => false, 'approved_at' => null]);
        $review->product->refreshRating();

        return back()->with('success', __('admin/review.messages.rejected'));
    }

    public function destroy(int|string $id)
    {
        $review = ProductReview::findOrFail($id);
        $product = $review->product;

        $review->delete();
        $product->refreshRating();

        return back()->with('success', __('admin/review.messages.deleted'));
    }
}
