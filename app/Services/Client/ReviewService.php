<?php

namespace App\Services\Client;

use App\Const\OrderConst;
use App\Const\PermissionConst;
use App\Const\ReviewConst;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Notifications\NewProductReview;
use App\Services\Admin\AdminNotifierService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReviewService
{
    public function paginateFor(Product $product, int $limit = ReviewConst::CLIENT_PER_PAGE): LengthAwarePaginator
    {
        return $product->reviews()
            ->where('is_approved', true)
            ->with('user:id,fullname')
            ->latest('id')
            ->paginate($limit, ['*'], 'reviews');
    }

    public function breakdownFor(Product $product): array
    {
        $rows = $product->reviews()
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $total = (int) $rows->sum();

        return collect(range(5, 1))
            ->mapWithKeys(fn ($star) => [$star => [
                'count' => (int) ($rows[$star] ?? 0),
                'percent' => $total > 0 ? round(((int) ($rows[$star] ?? 0)) / $total * 100) : 0,
            ]])
            ->all();
    }

    public function canReview(Product $product, ?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($product->reviews()->where('user_id', $user->id)->exists()) {
            return false;
        }

        return $this->purchasedOrder($product, $user) !== null;
    }

    public function store(Product $product, User $user, array $params): array
    {
        if ($product->reviews()->where('user_id', $user->id)->exists()) {
            return ['status' => false, 'message' => __('client.review.messages.already_reviewed')];
        }

        $order = $this->purchasedOrder($product, $user);

        if (! $order) {
            return ['status' => false, 'message' => __('client.review.messages.not_purchased')];
        }

        $images = $this->storeImages($params['images'] ?? []);

        try {
            $review = DB::transaction(fn () => ProductReview::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'rating' => (int) $params['rating'],
                'title' => $params['title'] ?? null,
                'comment' => $params['comment'] ?? null,
                'images' => $images ?: null,
                'is_approved' => false,
            ]));
        } catch (UniqueConstraintViolationException) {
            $this->deleteImages($images);

            return ['status' => false, 'message' => __('client.review.messages.already_reviewed')];
        } catch (Throwable $e) {
            $this->deleteImages($images);

            throw $e;
        }

        $this->notifyAdmins($review);

        return ['status' => true, 'message' => __('client.review.messages.submitted')];
    }

    protected function notifyAdmins(ProductReview $review): void
    {
        try {
            AdminNotifierService::notify(new NewProductReview($review->loadMissing(['product', 'user'])), PermissionConst::REVIEWS_MODERATE);
        } catch (Throwable $e) {
            Log::error(__METHOD__, ['message' => $e->getMessage(), 'review_id' => $review->id]);
        }
    }

    protected function deleteImages(array $paths): void
    {
        if ($paths !== []) {
            Storage::disk('public')->delete($paths);
        }
    }

    protected function storeImages(array $files): array
    {
        $paths = [];

        foreach (array_slice($files, 0, ReviewConst::MAX_IMAGES) as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $paths[] = $file->store('reviews', 'public');
        }

        return array_values(array_filter($paths));
    }

    protected function purchasedOrder(Product $product, User $user): ?Order
    {
        return $user->orders()
            ->where('status', OrderConst::STATUS_COMPLETED)
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->latest('id')
            ->first();
    }
}
