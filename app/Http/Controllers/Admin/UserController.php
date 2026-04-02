<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\GetUserRequest;
use App\Services\Admin\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(GetUserRequest $request)
    {
        $users = $this->userService->search($request->validated());

        return view('admin.pages.users.index', compact('users'));
    }

    public function show(int|string $id, $params = [])
    {
        $user = $this->userService->filter($params)->find($id);

        return view('admin.pages.users.show', compact('user'));
    }
}
