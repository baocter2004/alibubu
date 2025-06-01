<?php

use App\Models\AttributeValue;
use App\Repositories\BaseRepository;

class AttributeValueRepository extends BaseRepository
{
    public function getModel(): string
    {
        return AttributeValue::class;
    }
}