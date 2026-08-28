<?php

namespace App\Services\Client;

use App\Const\CouponConst;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Collection;

class CouponService
{
    public const SESSION_KEY = 'coupon_code';

    public function apply(string $code, Collection $items, float $subtotal, ?User $user = null): array
    {
        $coupon = $this->findByCode($code);

        if (! $coupon) {
            return $this->fail('not_found');
        }

        if ($reason = $this->rejectionReason($coupon, $items, $subtotal, $user)) {
            return $this->fail($reason);
        }

        session()->put(self::SESSION_KEY, $coupon->code);

        return [
            'status' => true,
            'message' => __('client.coupon.messages.applied'),
            'coupon' => $coupon,
            'discount' => $this->discountFor($coupon, $subtotal),
        ];
    }

    public function forget(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function current(Collection $items, float $subtotal, ?User $user = null): ?array
    {
        $code = session()->get(self::SESSION_KEY);

        if (! $code) {
            return null;
        }

        $coupon = $this->findByCode($code);

        if (! $coupon || $this->rejectionReason($coupon, $items, $subtotal, $user)) {
            $this->forget();

            return null;
        }

        return [
            'coupon' => $coupon,
            'discount' => $this->discountFor($coupon, $subtotal),
        ];
    }

    public function discountFor(Coupon $coupon, float $subtotal): float
    {
        $value = (float) $coupon->discount_value;

        $discount = $coupon->discount_type === CouponConst::PERCENT
            ? $subtotal * $value / 100
            : $value;

        $max = $coupon->restriction?->max_discount_value;

        if ($max !== null) {
            $discount = min($discount, (float) $max);
        }

        return (float) min(round($discount), $subtotal);
    }

    protected function findByCode(string $code): ?Coupon
    {
        return Coupon::with('restriction')
            ->whereRaw('UPPER(code) = ?', [mb_strtoupper(trim($code))])
            ->first();
    }

    protected function rejectionReason(Coupon $coupon, Collection $items, float $subtotal, ?User $user): ?string
    {
        if (! $coupon->is_active || $coupon->is_expired) {
            return 'inactive';
        }

        $now = now();

        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return 'not_started';
        }

        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return 'expired';
        }

        if ($coupon->usage_limit > 0 && $coupon->usage_count >= $coupon->usage_limit) {
            return 'exhausted';
        }

        if ($user && $coupon->users()->whereKey($user->id)->exists()) {
            return 'already_used';
        }

        $restriction = $coupon->restriction;

        if (! $restriction) {
            return null;
        }

        if ($restriction->min_order_value && $subtotal < (float) $restriction->min_order_value) {
            return 'min_order';
        }

        if ($restriction->valid_products && ! $this->matchesProducts($items, $restriction->valid_products)) {
            return 'not_applicable';
        }

        if ($restriction->valid_categories && ! $this->matchesCategories($items, $restriction->valid_categories)) {
            return 'not_applicable';
        }

        return null;
    }

    protected function matchesProducts(Collection $items, array $productIds): bool
    {
        return $items->pluck('product.id')->intersect($productIds)->isNotEmpty();
    }

    protected function matchesCategories(Collection $items, array $categoryIds): bool
    {
        return $items
            ->pluck('product')
            ->filter()
            ->flatMap(fn ($product) => $product->categories->pluck('id'))
            ->intersect($categoryIds)
            ->isNotEmpty();
    }

    protected function fail(string $reason): array
    {
        return [
            'status' => false,
            'message' => __('client.coupon.messages.' . $reason),
            'coupon' => null,
            'discount' => 0.0,
        ];
    }
}
