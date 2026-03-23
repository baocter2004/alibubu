<?php 

namespace App\Services\Admin;

use App\Repositories\UserRepository;

class UserEmployeeService
{
    function __construct(protected UserRepository $userRepository) {
    }
}