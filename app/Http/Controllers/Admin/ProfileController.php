<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\UpdatePasswordRequest;
use App\Http\Requests\Admin\Profile\UpdateProfileRequest;
use App\Services\Admin\ProfileService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profileService) {}

    public function edit()
    {
        return view('admin.pages.profile.index', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $this->profileService->updateProfile(Auth::guard('admin')->user(), $request->validated());

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', __('admin/profile.messages.profile_updated'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $this->profileService->updatePassword(
            Auth::guard('admin')->user(),
            $request->validated()['password']
        );

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', __('admin/profile.messages.password_updated'));
    }
}
