<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthLoginRequest;
use App\Http\Requests\User\AuthRegisterRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class ClientAuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showFormRegister()
    {
        return view('client.pages.auth.register');
    }

    public function handleRegister(AuthRegisterRequest $request)
    {
        $user = $this->authService->register($request->validated());

        if (! $user) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Đăng ký thất bại. Vui lòng thử lại.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('index')
            ->with('success', 'Đăng ký thành công! Vui lòng xác nhận email để có thể mua hàng.');
    }

    public function showFormLogin()
    {
        return view('client.pages.auth.login');
    }

    public function handleLogin(AuthLoginRequest $request)
    {
        if (! $this->authService->login($request->validated())) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', 'Email hoặc mật khẩu không chính xác.');
        }

        return redirect()
            ->intended(route('index'))
            ->with('success', 'Đăng nhập thành công!');
    }

    public function showFormForgotPassword()
    {
        return view('client.pages.auth.forgot-password');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        if (! $this->authService->sendResetLinkEmail($request->validated())) {
            return back()
                ->withInput()
                ->with('error', 'Không gửi được email đặt lại mật khẩu. Vui lòng thử lại sau.');
        }

        return back()->with('success', 'Đã gửi liên kết đặt lại mật khẩu. Vui lòng kiểm tra email của bạn!');
    }

    public function showFormNewPassword(Request $request, string $token)
    {
        return view('client.pages.auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function reset(ResetPasswordRequest $request)
    {
        if (! $this->authService->reset($request->validated())) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
        }

        return redirect()
            ->route('auth.client.showFormLogin')
            ->with('success', 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('index')
            ->with('success', 'Đăng xuất thành công!');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);

            return redirect()
                ->route('auth.client.showFormLogin')
                ->with('error', 'Không thể đăng nhập bằng Google. Vui lòng thử lại.');
        }

        $user = $this->authService->google($googleUser);

        if (! $user) {
            return redirect()
                ->route('auth.client.showFormLogin')
                ->with('error', 'Tài khoản bị khóa hoặc không hợp lệ.');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('index')
            ->with('success', 'Đăng nhập thành công!');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->route('index')
                ->with('error', 'Email đã được xác minh trước đó.');
        }

        $request->fulfill();

        return redirect()
            ->route('verification.success')
            ->with('success', 'Xác minh email thành công!');
    }

    public function showVerifySuccess()
    {
        return view('common.verification.success');
    }
}
