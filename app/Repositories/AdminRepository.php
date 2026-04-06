<?php

namespace App\Repositories;

use App\Models\Admin;

class AdminRepository extends BaseRepository
{
    public function getModel(): Admin
    {
        if(empty($this->model)) {
            $this->model = app()->make(Admin::class);
        }
        return $this->model;
    }
}
