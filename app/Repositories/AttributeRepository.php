<?php

use App\Models\Attribute;
use App\Repositories\BaseRepository;

class AttributeRepository extends BaseRepository
{
    public function getModel(): string 
    {
        return Attribute::class;
    }
}