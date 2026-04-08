<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\GetUserRequest;
use App\Http\Requests\Admin\Users\PostUserRequest;
use App\Models\Province;
use App\Services\Admin\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(GetUserRequest $request)
    {
        session()->forget('user_data');
        $users = $this->userService->search($request->validated());

        return view('admin.pages.users.index', compact('users'));
    }

    public function create()
    {
        $data = session()->get('user_data');
        $provinces = Province::select('id', 'name')->get();

        return view('admin.pages.users.create', compact('data', 'provinces'));
    }

    public function confirm(PostUserRequest $request)
    {
        $data = $request->validated();

        session()->put('user_data', $data);
        return redirect()->route('admin.users.confirm-detail');
    }

    public function confirmDetail()
    {
        $data = session()->get('user_data');

        if (!$data) {
            return redirect()->route('admin.users.create');
        }

        $data['user_addresses'] = $this->userService->mapAddressName($data['user_addresses'] ?? []);

        return view('admin.pages.users.confirms.create-confirm', compact('data'));
    }

    public function store()
    {
        $data = session()->get('user_data');

        if (!$data) {
            return redirect()->route('admin.users.create');
        }

        $this->userService->create($data);

        session()->forget('user_data');

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(int|string $id)
    {
        $params = [
            'relates' => ['userAddresses']
        ];
        $user = $this->userService->filter($params)->find($id);

        $provinces = Province::select('id', 'name')->get();

        return view('admin.pages.users.edit', compact('user', 'provinces'));
    }

    public function update(PostUserRequest $request, int|string $id)
    {
        $data = $request->validated();

        $this->userService->update($id, $data);

        return redirect()->route('admin.users.show', $id)
            ->with('success', 'User updated successfully.');
    }

    public function show(int|string $id)
    {
        $params = [
            'relates' => ['userAddresses']
        ];
        $user = $this->userService->filter($params)->find($id);

        return view('admin.pages.users.show', compact('user'));
    }

    public function destroy(int|string $id)
    {
        $this->userService->delete($id);

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
