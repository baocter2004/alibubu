<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Administrator\PostAdministratorRequest;
use App\Models\Admin;
use App\Services\Admin\AdministratorService;
use Illuminate\Support\Facades\Auth;

class AdministratorController extends Controller
{
    public function __construct(protected AdministratorService $administratorService) {}

    public function index()
    {
        $actor = $this->actor();

        return view('admin.pages.administrators.index', [
            'administrators' => $this->administratorService->paginate(),
            'canManage' => fn (Admin $administrator) => $this->administratorService->canManage($actor, $administrator),
        ]);
    }

    public function create()
    {
        return view('admin.pages.administrators.create', [
            'roles' => $this->administratorService->assignableRoles($this->actor()),
        ]);
    }

    public function store(PostAdministratorRequest $request)
    {
        $result = $this->administratorService->create($this->actor(), $request->validated());

        if (! $result['status']) {
            return back()->withInput($request->except('password', 'password_confirmation'))->with('error', $result['message']);
        }

        return redirect()->route('admin.administrators.index')->with('success', $result['message']);
    }

    public function edit(string $id)
    {
        $actor = $this->actor();

        return view('admin.pages.administrators.edit', [
            'administrator' => $this->administratorService->findManageable($actor, $id),
            'roles' => $this->administratorService->assignableRoles($actor),
        ]);
    }

    public function update(PostAdministratorRequest $request, string $id)
    {
        $result = $this->administratorService->update($this->actor(), $id, $request->validated());

        if (! $result['status']) {
            return back()->withInput($request->except('password', 'password_confirmation'))->with('error', $result['message']);
        }

        return redirect()->route('admin.administrators.index')->with('success', $result['message']);
    }

    public function destroy(string $id)
    {
        $result = $this->administratorService->delete($this->actor(), $id);

        if (! $result['status']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('admin.administrators.index')->with('success', $result['message']);
    }

    protected function actor(): Admin
    {
        return Auth::guard('admin')->user();
    }
}
