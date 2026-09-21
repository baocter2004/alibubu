<?php

namespace App\Http\Controllers\Auth;

use App\Const\SecurityConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AuthLoginRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
use App\Services\Auth\AuthAdminService;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function __construct(protected AuthAdminService $authService) {}

    public function showFormLogin()
    {
        return view('admin.pages.auth.login');
    }

    public function handleLogin(AuthLoginRequest $request)
    {
        $status = $this->authService->login($request, $request->validated());

        if ($status === SecurityConst::LOGIN_INACTIVE) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', __('admin/administrator.messages.account_inactive'));
        }

        if ($status !== SecurityConst::LOGIN_OK) {
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
        $this->authService->sendResetLinkEmail($request->validated());

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

    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()
            ->route('auth.admin.showFormLogin')
            ->with('success', __('admin/auth.messages.logged_out'));
    }
}
