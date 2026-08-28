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
        $statuses = UserConst::statuses();
        $roles = UserConst::roles();

        return view('admin.pages.users.index', compact('users', 'statuses', 'roles'));
    }

    public function trash(GetUserRequest $request)
    {
        $users = $this->userService->searchTrashed($request->validated());
        $statuses = UserConst::statuses();
        $roles = UserConst::roles();

        return view('admin.pages.users.trash', compact('users', 'statuses', 'roles'));
    }

    public function create()
    {
        $data = session()->get('user_data');
        $provinces = Province::select('id', 'name')->orderBy('name')->get();

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
        $user = $this->userService->filter(['relates' => ['userAddresses']])->find($id);

        abort_if(! $user, 404);

        $provinces = Province::select('id', 'name')->orderBy('name')->get();

        return view('admin.pages.users.edit', compact('user', 'provinces'));
    }

    public function save()
    {
        $data = session()->get('user_data');

        if (!$data) {
            return redirect()->route('admin.users.create');
        }

        if (! empty($data['id'])) {
            $this->userService->update($data['id'], $data);
            $message = __('admin/user.messages.updated');
        } else {
            $this->userService->create($data);
            $message = __('admin/user.messages.created');
        }

        session()->forget('user_data');

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function show(int|string $id)
    {
        $user = $this->userService->filter(['relates' => ['userAddresses']])->find($id);

        abort_if(! $user, 404);

        return view('admin.pages.users.show', compact('user'));
    }

    public function destroy(int|string $id)
    {
        $result = $this->userService->delete($id);

        if (! $result['status']) {
            return redirect()->route('admin.users.index')->with('error', $result['message']);
        }

        return redirect()->route('admin.users.index')->with('success', $result['message']);
    }

    public function forceDestroy(int|string $id)
    {
        if (! $this->userService->forceDelete($id)) {
            return redirect()
                ->route('admin.users.trash')
                ->with('error', __('admin/user.messages.not_found'));
        }

        return redirect()
            ->route('admin.users.trash')
            ->with('success', __('admin/user.messages.force_deleted'));
    }

    public function restore(int|string $id)
    {
        $this->userService->restore($id);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('admin/user.messages.restored'));
    }
}
