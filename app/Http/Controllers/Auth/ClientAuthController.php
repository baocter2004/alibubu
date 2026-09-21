<?php

namespace App\Http\Controllers\Auth;

use App\Const\SecurityConst;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AuthLoginRequest;
use App\Http\Requests\User\AuthRegisterRequest;
use App\Http\Requests\User\ForgotPasswordRequest;
use App\Http\Requests\User\ResetPasswordRequest;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class ClientAuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function showFormRegister()
    {
        return view('client.pages.auth.register');
    }

    public function handleRegister(Request $request, AuthRegisterRequest $registerRequest)
    {
        $user = $this->authService->register($registerRequest->validated());

        if (! $user) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', __('client_auth.messages.register_failed'));
        }

        $this->authService->loginUser($request, $user);

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
        $result = $this->authService->login($request, $request->validated());

        if ($result['status'] === SecurityConst::LOGIN_INACTIVE) {
            return back()
                ->withInput($request->except('password'))
                ->with('error', $result['message']);
        }

        if ($result['status'] !== SecurityConst::LOGIN_OK) {
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
        $this->authService->sendResetLinkEmail($request->validated());

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
        $this->authService->logout($request);

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

        $result = $this->authService->google($request, $googleUser);

        switch ($result['status']) {
            case SecurityConst::GOOGLE_LOGGED_IN:
            case SecurityConst::GOOGLE_LINKED:
            case SecurityConst::GOOGLE_CREATED:
                $this->authService->loginUser($request, $result['user']);

                return redirect()
                    ->intended(route('index'))
                    ->with('success', __('client_auth.messages.logged_in'));

            case SecurityConst::GOOGLE_LINK_REQUIRED:
                return redirect()
                    ->route('auth.client.showFormLogin')
                    ->with('error', __('client_auth.messages.google_link_required', ['email' => $result['email']]));

            case SecurityConst::GOOGLE_CONFLICT:
                return redirect()
                    ->route('auth.client.showFormLogin')
                    ->with('error', __('client_auth.messages.google_conflict'));

            case SecurityConst::GOOGLE_UNVERIFIED:
                return redirect()
                    ->route('auth.client.showFormLogin')
                    ->with('error', __('client_auth.messages.google_unverified'));

            case SecurityConst::GOOGLE_INACTIVE:
                return redirect()
                    ->route('auth.client.showFormLogin')
                    ->with('error', $result['message']);

            default:
                return redirect()
                    ->route('auth.client.showFormLogin')
                    ->with('error', __('client_auth.messages.google_failed'));
        }
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

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()
                ->route('index')
                ->with('error', __('client_auth.messages.email_already_verified'));
        }

        $this->authService->sendVerificationEmail($user);

        return back()->with('success', __('client_auth.messages.verification_resent'));
    }
}
