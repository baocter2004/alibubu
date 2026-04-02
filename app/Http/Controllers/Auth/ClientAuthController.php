<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthLoginRequest;
use App\Http\Requests\User\AuthRegisterRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class ClientAuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showFormRegister()
    {
        return view('client.pages.auth.register');
    }

    public function HandleRegister(AuthRegisterRequest $authRegisterRequest)
    {
        $result = $this->authService->register($authRegisterRequest->validated());

        if ($result) {
            Auth::login($result);
            return redirect()->route('index')->with('success', 'Đăng ký thành công!  Vui Lòng Xác Nhận Email Để Có Thể Mua Hàng.');
        } else {
            return back()->with('error', 'Đăng ký thất bại. Vui lòng thử lại.');
        }
    }

    public function showFormLogin()
    {
        return view('client.pages.auth.login');
    }

    public function showFormForgotPassword()
    {
        return view('client.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $params = $request->validate([
            'email' => [
                'required',
                'email'
            ],
        ]);

        $result = $this->authService->sendResetLinkEmail($params);
        if ($result) {
            return redirect()
                ->route('index')
                ->with('success', 'Email đã được gửi thành công. Vui lòng kiểm tra hộp thư đến của bạn!');
        } else {
            return back()
                ->withInput()
                ->with('error', 'Gửi Email thất bại. Vui lòng thử lại sau.');
        }
    }

    public function showFormNewPassword($token, $email)
    {
        return view('client.auth.reset-password', compact('token', 'email'));
    }

    public function reset(ResetPasswordRequest $request)
    {
        $result = $this->authService->reset($request->validated());

        if ($result) {
            return redirect()
                ->route('auth.client.showFormLogin')
                ->with('success', 'Đổi mật khẩu mới thành công . Vui lòng đăng nhập lại !');
        } else {
            return back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra. Vui lòng thử lại sau.');
        }
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('index')->with('success', 'Đăng xuất thành công!');
    }

    public function handleLogin(AuthLoginRequest $request)
    {
        $result = $this->authService->login($request->validated());

        if ($result) {
            return redirect()->route('index')->with('success', 'Đăng Nhập thành công!');
        } else {
            return back()->with('error', 'Đăng Nhập thất bại. Vui lòng thử lại.');
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = $this->authService->google($googleUser);

        if ($user) {
            Auth::login($user);
            return redirect()
                ->route('index')
                ->with('success', 'Đăng nhập thành công! ');
        } else {
            return redirect()
                ->route('auth.client.showFormLogin')
                ->with('error', 'Tài khoản bị khóa hoặc không hợp lệ.');
        }
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()
                    ->route('index')
                    ->with('error', 'Email đã được xác minh trước đó.');
            }
            $request->fulfill();

            return redirect()
                ->route('verification.success')
                ->with('status', 'Xác minh email thành công!');
        } catch (\Throwable $th) {
            Log::error('VerifyEmailError', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine(),
            ]);
            return redirect()
                ->route('index')
                ->with('error', 'Có lỗi khi xác minh email. Vui lòng thử lại.');
        }
    }

    public function showVerifySuccess()
    {
        return view('common.verification.success');
    }
}
