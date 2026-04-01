<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    public function getModel(): Category
    {
        if (empty($this->model)) {
            $this->model = app()->make(Category::class);
        }
        return $this->model;
    }
}
