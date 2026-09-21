<?php

namespace App\Repositories;

use App\Models\AdminActivityLog;

class AdminActivityLogRepository extends BaseRepository
{
    public function getModel(): AdminActivityLog
    {
        if (empty($this->model)) {
            $this->model = app()->make(AdminActivityLog::class);
        }

        return $this->model;
    }
}
