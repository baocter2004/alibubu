<?php

namespace App\Const;

class MembershipConst
{
    const TIER_MEMBER = 'member';
    const TIER_SILVER = 'silver';
    const TIER_GOLD = 'gold';
    const TIER_DIAMOND = 'diamond';

    const POINTS_PER_UNIT = 100000;

    const TYPE_ORDER = 'order';

    const TYPE_ADJUSTMENT = 'adjustment';

    public static function tiers(): array
    {
        return [
            self::TIER_MEMBER => 0,
            self::TIER_SILVER => 500,
            self::TIER_GOLD => 2000,
            self::TIER_DIAMOND => 5000,
        ];
    }

    public static function discountRates(): array
    {
        return [
            self::TIER_MEMBER => 0,
            self::TIER_SILVER => 2,
            self::TIER_GOLD => 3,
            self::TIER_DIAMOND => 5,
        ];
    }

    public static function discountRate(string $tier): int
    {
        return self::discountRates()[$tier] ?? 0;
    }

    public static function discountFor(string $tier, float $subtotal): float
    {
        $rate = self::discountRate($tier);

        return $rate > 0 ? round($subtotal * $rate / 100) : 0.0;
    }

    public static function tierFor(int $points): string
    {
        $matched = self::TIER_MEMBER;

        foreach (self::tiers() as $tier => $threshold) {
            if ($points >= $threshold) {
                $matched = $tier;
            }
        }

        return $matched;
    }

    public static function nextTier(int $points): ?string
    {
        foreach (self::tiers() as $tier => $threshold) {
            if ($points < $threshold) {
                return $tier;
            }
        }

        return null;
    }

    public static function threshold(string $tier): int
    {
        return self::tiers()[$tier] ?? 0;
    }

    public static function label(string $tier): string
    {
        return __('enum.membership.tier.' . $tier);
    }

    public static function badgeClass(string $tier): string
    {
        return match ($tier) {
            self::TIER_DIAMOND => 'bg-sky-100 text-sky-700',
            self::TIER_GOLD => 'bg-amber-100 text-amber-700',
            self::TIER_SILVER => 'bg-slate-200 text-slate-700',
            default => 'bg-muted text-muted-foreground',
        };
    }

    public static function pointsFor(float $amount): int
    {
        return (int) floor($amount / self::POINTS_PER_UNIT);
    }
}
