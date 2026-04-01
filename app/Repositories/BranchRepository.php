<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Repositories\BaseRepository;

class BranchRepository extends BaseRepository
{
    public function getModel(): Branch
    {
        if (empty($this->model)) {
            $this->model = app()->make(Branch::class);
        }
        return $this->model;
    }
}