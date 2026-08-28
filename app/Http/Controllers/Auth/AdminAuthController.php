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
                ->with('error', 'Email hoặc mật khẩu không chính xác.');
        }

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('success', 'Đăng nhập thành công!');
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
                ->with('error', 'Không gửi được email đặt lại mật khẩu. Vui lòng thử lại sau.');
        }

        return back()->with('success', 'Đã gửi liên kết đặt lại mật khẩu. Vui lòng kiểm tra email.');
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
                ->with('error', 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
        }

        return redirect()
            ->route('auth.admin.showFormLogin')
            ->with('success', 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại!');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('auth.admin.showFormLogin')
            ->with('success', 'Đăng xuất thành công!');
    }
}
