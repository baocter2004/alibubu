<?php 

namespace App\Services\Client;

use App\Repositories\UserRepository;
class UserService
{
    public function __construct(protected UserRepository $userRepository) {
    }

    public function getUser(string $id) {
        return $this->userRepository->findById($id);
    }
}