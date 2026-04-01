<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository
{
    public function getModel(): Tag
    {
        if (empty($this->model)) {
            $this->model = app()->make(Tag::class);
        }
        return $this->model;
    }
}
