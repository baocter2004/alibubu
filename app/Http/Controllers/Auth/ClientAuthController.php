<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthRegisterRequest;
use App\Repositories\UserRepository;
use App\Services\Client\AuthService;
use App\Services\Client\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;

class ClientAuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService, UserService $userService)
    {
        $this->authService = $authService;
    }

    public function showFormRegister()
    {
        return view('client.auth.register');
    }

    public function HandleRegister(AuthRegisterRequest $authRegisterRequest)
    {
        $result = $this->authService->register($authRegisterRequest->validated());

        if ($result) {
            Auth::login($result);
            return redirect()->route('index')->with('success', 'Đăng ký thành công!');
        } else {
            return back()->with('error', 'Đăng ký thất bại. Vui lòng thử lại.');
        }
    }

    public function showFormLogin()
    {
        return view('client.auth.login');
    }

    public function showFormForgotPassword()
    {
        return view('client.auth.forgot-password');
    }

    public function logout()
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('index')->with('success', 'Đăng xuất thành công!');
    }

    public function handleLogin(Request $request)
    {
        $result = $this->authService->login($request->validate([
            'email' => [
                'email',
                'required',
                Rule::exists('users', 'email')
            ],
            'password' => ['required', 'string', 'min:6']
        ]));

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
            return redirect()->route('index')->with('success', 'Đăng nhập thành công!');
        } else {
            return redirect()->route('auth.client.showFormLogin')->with('error', 'Tài khoản bị khóa hoặc không hợp lệ.');
        }
    }
}
