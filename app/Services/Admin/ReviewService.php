<?php

namespace App\Services\Admin;

use App\Const\ReviewConst;
use App\Models\Product;
use App\Models\ProductReview;
use App\Notifications\ReviewModerated;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReviewService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $status = $filters['status'] ?? null;
        $keyword = trim((string) ($filters['keyword'] ?? ''));

        return ProductReview::query()
            ->with(['product:id,name,slug,thumbnail', 'user:id,fullname,email'])
            ->when($status === ReviewConst::STATUS_PENDING, fn ($query) => $query->pending())
            ->when($status === ReviewConst::STATUS_APPROVED, fn ($query) => $query->approved())
            ->when($status === ReviewConst::STATUS_REJECTED, fn ($query) => $query->rejected())
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('title', 'like', "%{$keyword}%")
                        ->orWhere('comment', 'like', "%{$keyword}%")
                        ->orWhereHas('product', fn ($product) => $product->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->latest('id')
            ->paginate(ReviewConst::ADMIN_PER_PAGE);
    }

    public function stats(): array
    {
        return [
            ReviewConst::STATUS_PENDING => ProductReview::query()->pending()->count(),
            ReviewConst::STATUS_APPROVED => ProductReview::query()->approved()->count(),
            ReviewConst::STATUS_REJECTED => ProductReview::query()->rejected()->count(),
        ];
    }

    public function approve(string $id): array
    {
        $review = ProductReview::query()->with(['product', 'user', 'order'])->find($id);

        if (! $review) {
            return ['status' => false, 'message' => __('admin/review.messages.not_found')];
        }

        $changed = ! $review->is_approved;

        DB::transaction(function () use ($review) {
            $review->update([
                'is_approved' => true,
                'approved_at' => now(),
                'rejected_at' => null,
                'rejection_reason' => null,
            ]);

            $this->refreshRating($review);
        });

        if ($changed) {
            $this->notifyAuthor($review, true);
        }

        return ['status' => true, 'message' => __('admin/review.messages.approved')];
    }

    public function reject(string $id, ?string $reason = null): array
    {
        $review = ProductReview::query()->with(['product', 'user', 'order'])->find($id);

        if (! $review) {
            return ['status' => false, 'message' => __('admin/review.messages.not_found')];
        }

        $reason = filled($reason) ? trim($reason) : null;
        $changed = ! $review->isRejected();

        DB::transaction(function () use ($review, $reason) {
            $review->update([
                'is_approved' => false,
                'approved_at' => null,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->refreshRating($review);
        });

        if ($changed) {
            $this->notifyAuthor($review, false, $reason);
        }

        return ['status' => true, 'message' => __('admin/review.messages.rejected')];
    }

    public function delete(string $id): array
    {
        $review = ProductReview::query()->find($id);

        if (! $review) {
            return ['status' => false, 'message' => __('admin/review.messages.not_found')];
        }

        $images = $review->images ?? [];

        DB::transaction(function () use ($review) {
            $review->delete();
            $this->refreshRating($review);
        });

        if ($images !== []) {
            Storage::disk('public')->delete($images);
        }

        return ['status' => true, 'message' => __('admin/review.messages.deleted')];
    }

    protected function refreshRating(ProductReview $review): void
    {
        Product::withTrashed()->find($review->product_id)?->refreshRating();
    }

    protected function notifyAuthor(ProductReview $review, bool $approved, ?string $reason = null): void
    {
        if (! $review->user) {
            return;
        }

        try {
            $review->user->notify(new ReviewModerated($review, $approved, $reason));
        } catch (Throwable $e) {
            Log::error(__METHOD__, ['message' => $e->getMessage(), 'review_id' => $review->id]);
        }
    }
}
