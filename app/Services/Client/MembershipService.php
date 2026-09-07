<?php

namespace App\Services\Client;

use App\Const\MembershipConst;
use App\Models\LoyaltyPointTransaction;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MembershipService
{
    public function awardForOrder(Order $order): int
    {
        if (! $order->user_id) {
            return 0;
        }

        $already = LoyaltyPointTransaction::where('order_id', $order->id)
            ->where('type', MembershipConst::TYPE_ORDER)
            ->exists();

        if ($already) {
            return 0;
        }

        $points = MembershipConst::pointsFor((float) $order->total_amount);

        if ($points < 1) {
            return 0;
        }

        DB::transaction(function () use ($order, $points) {
            LoyaltyPointTransaction::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'points' => $points,
                'type' => MembershipConst::TYPE_ORDER,
                'description' => $order->code,
                'earned_at' => now(),
            ]);

            $this->refresh(User::find($order->user_id));
        });

        return $points;
    }

    public function pointsInWindow(User $user, ?Carbon $now = null): int
    {
        $now = $now ?: now();
        $since = $now->copy()->subMonths((int) config('membership.window_months', 1));

        return (int) $user->loyaltyTransactions()
            ->where('earned_at', '>=', $since)
            ->sum('points');
    }

    public function refresh(?User $user, ?Carbon $now = null): ?User
    {
        if (! $user) {
            return null;
        }

        $points = $this->pointsInWindow($user, $now);

        $user->forceFill([
            'loyalty_points' => $points,
            'membership_tier' => MembershipConst::tierFor($points),
        ])->save();

        return $user;
    }

    public function review(User $user, ?Carbon $now = null): array
    {
        $now = $now ?: now();
        $before = $user->membership_tier ?: MembershipConst::TIER_MEMBER;

        $this->refresh($user, $now);

        $user->forceFill(['tier_reviewed_at' => $now])->save();

        $after = $user->fresh()->membership_tier;

        return [
            'from' => $before,
            'to' => $after,
            'changed' => $before !== $after,
            'demoted' => MembershipConst::threshold($after) < MembershipConst::threshold($before),
        ];
    }

    public function reviewDue(?Carbon $now = null): array
    {
        $now = $now ?: now();
        $months = (int) config('membership.review_months', 1);
        $summary = ['reviewed' => 0, 'promoted' => 0, 'demoted' => 0];

        User::query()
            ->where(fn ($query) => $query
                ->whereNull('tier_reviewed_at')
                ->orWhere('tier_reviewed_at', '<=', $now->copy()->subMonths($months)))
            ->chunkById(200, function ($users) use (&$summary, $now) {
                foreach ($users as $user) {
                    $result = $this->review($user, $now);
                    $summary['reviewed']++;

                    if ($result['changed']) {
                        $summary[$result['demoted'] ? 'demoted' : 'promoted']++;
                    }
                }
            });

        return $summary;
    }
}
