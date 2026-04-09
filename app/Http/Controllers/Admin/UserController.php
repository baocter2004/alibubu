<?php

namespace App\Http\Controllers\Admin;

use App\Const\UserConst;
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
        $statuses = UserConst::STATUS;
        $roles = UserConst::ROLE;

        return view('admin.pages.users.index', compact('users', 'statuses', 'roles'));
    }

    public function trash(GetUserRequest $request)
    {
        $users = $this->userService->searchTrashed($request->validated());
        $statuses = UserConst::STATUS;
        $roles = UserConst::ROLE;

        return view('admin.pages.users.trash', compact('users', 'statuses', 'roles'));
    }

    public function create()
    {
        $data = session()->get('user_data');
        $provinces = Province::select('id', 'name')->get();

        return view('admin.pages.users.create', compact('data', 'provinces'));
    }

    public function confirm(PostUserRequest $request, $id = null)
    {
        $data = $request->validated();
        $data['id'] = $id;

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

        return view('admin.pages.users.confirms.form-confirm', compact('data'));
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

    public function save()
    {
        $data = session()->get('user_data');

        if (!$data) {
            return redirect()->route('admin.users.create');
        }

        if (!empty($data['id'])) {
            // update
            $this->userService->update($data['id'], $data);
            $message = 'User updated successfully.';
        } else {
            // create
            $this->userService->create($data);
            $message = 'User created successfully.';
        }

        session()->forget('user_data');

        return redirect()->route('admin.users.index')->with('success', $message);
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

    public function forceDestroy(int|string $id)
    {
        $this->userService->forceDelete($id);

        return redirect()->route('admin.users.index')->with('success', 'User permanently deleted successfully.');
    }

    public function restore(int|string $id)
    {
        $this->userService->restore($id);

        return redirect()->route('admin.users.index')->with('success', 'User restored successfully.');
    }
}
