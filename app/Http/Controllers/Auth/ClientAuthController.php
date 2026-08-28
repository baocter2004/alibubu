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
                ->with('error', __('client_auth.messages.register_failed'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('index')
            ->with('success', __('client_auth.messages.registered'));
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
                ->with('error', __('client_auth.messages.login_failed'));
        }

        return redirect()
            ->intended(route('index'))
            ->with('success', __('client_auth.messages.logged_in'));
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
                ->with('error', __('client_auth.messages.reset_link_failed'));
        }

        return back()->with('success', __('client_auth.messages.reset_link_sent'));
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
                ->with('error', __('client_auth.messages.reset_failed'));
        }

        return redirect()
            ->route('auth.client.showFormLogin')
            ->with('success', __('client_auth.messages.reset_success'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('index')
            ->with('success', __('client_auth.messages.logged_out'));
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
                ->with('error', __('client_auth.messages.google_failed'));
        }

        $user = $this->authService->google($googleUser);

        if (! $user) {
            return redirect()
                ->route('auth.client.showFormLogin')
                ->with('error', __('client_auth.messages.account_locked'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('index')
            ->with('success', __('client_auth.messages.logged_in'));
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->route('index')
                ->with('error', __('client_auth.messages.email_already_verified'));
        }

        $request->fulfill();

        return redirect()
            ->route('verification.success')
            ->with('success', __('client.verification.title'));
    }

    public function showVerifySuccess()
    {
        return view('common.verification.success');
    }
}
