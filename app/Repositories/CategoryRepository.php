<?php

use App\Models\Category;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    public function getModel(): string
    {
        return Category::class;
    }
}