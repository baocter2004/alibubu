<?php

namespace App\Repositories;

use App\Models\UserAddress;

class UserAddressRepository extends BaseRepository
{
    public function getModel(): UserAddress
    {
        if(empty($this->model)) {
            $this->model = app()->make(UserAddress::class);
        }
        return $this->model;
    }
}
