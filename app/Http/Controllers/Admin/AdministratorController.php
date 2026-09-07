<?php

namespace App\Http\Controllers\Admin;

use App\Const\AdminConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Administrator\PostAdministratorRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AdministratorController extends Controller
{
    public function index()
    {
        $administrators = Admin::query()->latest('created_at')->paginate(20);

        return view('admin.pages.administrators.index', [
            'administrators' => $administrators,
        ]);
    }

    public function create()
    {
        return view('admin.pages.administrators.create', [
            'roles' => AdminConst::roles(),
        ]);
    }

    public function store(PostAdministratorRequest $request)
    {
        Admin::create($request->validated());

        return redirect()->route('admin.administrators.index')->with('success', __('admin/administrator.messages.created'));
    }

    public function edit(string $id)
    {
        $administrator = Admin::query()->findOrFail($id);

        return view('admin.pages.administrators.edit', [
            'administrator' => $administrator,
            'roles' => AdminConst::roles(),
        ]);
    }

    public function update(PostAdministratorRequest $request, string $id)
    {
        $administrator = Admin::query()->findOrFail($id);
        $data = $request->validated();
        $currentId = (string) Auth::guard('admin')->id();

        if ((string) $administrator->id === $currentId && (int) $data['role'] !== AdminConst::ROLE_SUPER_ADMIN) {
            return back()->withInput()->with('error', __('admin/administrator.messages.cannot_demote_self'));
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $administrator->update($data);

        return redirect()->route('admin.administrators.index')->with('success', __('admin/administrator.messages.updated'));
    }

    public function destroy(string $id)
    {
        $administrator = Admin::query()->findOrFail($id);
        $currentId = (string) Auth::guard('admin')->id();

        if ((string) $administrator->id === $currentId) {
            return back()->with('error', __('admin/administrator.messages.cannot_delete_self'));
        }

        if ((int) $administrator->role === AdminConst::ROLE_SUPER_ADMIN && Admin::query()->where('role', AdminConst::ROLE_SUPER_ADMIN)->count() <= 1) {
            return back()->with('error', __('admin/administrator.messages.cannot_delete_last_super_admin'));
        }

        $administrator->delete();

        return redirect()->route('admin.administrators.index')->with('success', __('admin/administrator.messages.deleted'));
    }
}
