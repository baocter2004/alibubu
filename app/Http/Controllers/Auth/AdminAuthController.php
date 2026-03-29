<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminAuthController extends Controller
{
    public function __construct(protected AuthAdminService $authService)
    {
    }

    public function showFormLogin()
    {
        return view('admin.auth.login');
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
            return redirect()->route('admin.dashboard')->with('success', 'Đăng Nhập thành công!');
        } else {
            return back()->with('error', 'Đăng Nhập thất bại. Vui lòng thử lại.');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('index')->with('success', 'Đăng xuất thành công!');
    }
}
