<?php

use App\Models\Tag;
use App\Repositories\BaseRepository;

class TagRepository extends BaseRepository
{
    public function getModel(): string
    {
        return Tag::class;
    }
}
