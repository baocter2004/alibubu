<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AuthLoginRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
use App\Services\Auth\AuthAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function __construct(protected AuthAdminService $authService) {}

    public function showFormLogin()
    {
        return view('admin.pages.auth.login');
    }

    public function handleLogin(AuthLoginRequest $request)
    {
        if (! $this->authService->login($request->validated())) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', __('admin/auth.messages.failed'));
        }

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('success', __('admin/auth.messages.logged_in'));
    }

    public function showFormForgotPassword()
    {
        return view('admin.pages.auth.forgot-password');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        if (! $this->authService->sendResetLinkEmail($request->validated())) {
            return back()
                ->withInput()
                ->with('error', __('admin/auth.messages.reset_link_failed'));
        }

        return back()->with('success', __('admin/auth.messages.reset_link_sent'));
    }

    public function showFormNewPassword(Request $request, string $token)
    {
        return view('admin.pages.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function updatePassword(ResetPasswordRequest $request)
    {
        if (! $this->authService->resetPassword($request->validated())) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', __('admin/auth.messages.reset_failed'));
        }

        return redirect()
            ->route('auth.admin.showFormLogin')
            ->with('success', __('admin/auth.messages.reset_success'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('auth.admin.showFormLogin')
            ->with('success', __('admin/auth.messages.logged_out'));
    }
}
