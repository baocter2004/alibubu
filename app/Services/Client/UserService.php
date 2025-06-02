<?php 

namespace App\Services\Client;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $userRepository;

    function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getUser(string $id) {
        return $this->userRepository->findById($id);
    }
}