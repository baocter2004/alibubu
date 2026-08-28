<?php

namespace App\Services\Client;

use App\Const\OrderConst;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function paginateFor(Product $product, int $limit = 5): LengthAwarePaginator
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

        DB::transaction(function () use ($product, $user, $order, $params) {
            ProductReview::create([
                'product_id' => $product->id,
                'user_id' => $user->id,
                'order_id' => $order->id,
                'rating' => (int) $params['rating'],
                'title' => $params['title'] ?? null,
                'comment' => $params['comment'] ?? null,
                'is_approved' => false,
            ]);
        });

        return ['status' => true, 'message' => __('client.review.messages.submitted')];
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
