<?php

use App\Models\Branch;
use App\Repositories\BaseRepository;

class BranchRepository extends BaseRepository
{
    public function getModel(): string
    {
        return Branch::class;
    }
}