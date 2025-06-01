<?php 

namespace App\Services\Admin;

use App\Repositories\UserRepository;

class UserEmployeeService
{
    protected $userRepository;

    function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }
}