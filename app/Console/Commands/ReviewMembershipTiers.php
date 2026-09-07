<?php

namespace App\Console\Commands;

use App\Services\Client\MembershipService;
use Illuminate\Console\Command;

class ReviewMembershipTiers extends Command
{
    protected $signature = 'membership:review';

    protected $description = 'Re-evaluate membership tiers whose review period has elapsed';

    public function handle(MembershipService $membershipService): int
    {
        $summary = $membershipService->reviewDue();

        $this->info(sprintf(
            'Reviewed %d members: %d promoted, %d demoted.',
            $summary['reviewed'],
            $summary['promoted'],
            $summary['demoted']
        ));

        return self::SUCCESS;
    }
}
