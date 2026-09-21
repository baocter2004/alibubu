<?php

namespace App\Services\Auth;

use App\Const\SecurityConst;
use App\Models\User;
use App\Notifications\GoogleAccountLinked;
use App\Notifications\PasswordChanged;
use App\Repositories\UserRepository;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\AbstractUser as SocialiteUser;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected SessionService $sessionService,
    ) {}

    public function register(array $params): ?User
    {
        try {
            $user = $this->userRepository->create([
                'fullname' => $params['fullname'],
                'email' => $params['email'],
                'password' => Hash::make($params['password']),
            ]);
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);

            return null;
        }

        $this->sendVerificationEmail($user);

        return $user;
    }

    public function sendVerificationEmail(User $user): bool
    {
        try {
            $user->sendEmailVerificationNotification();

            return true;
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);

            return false;
        }
    }

    public function login(Request $request, array $params): array
    {
        $guard = Auth::guard('user');
        $credentials = [
            'email' => $params['email'],
            'password' => $params['password'],
        ];

        if ($guard->attemptWhen($credentials, fn (User $user) => $user->isActive(), (bool) ($params['remember'] ?? false))) {
            $this->sessionService->regenerate($request, 'user');

            return [
                'status' => SecurityConst::LOGIN_OK,
                'google_linked' => $this->completePendingGoogleLink($request, $guard->user()),
            ];
        }

        $user = $guard->getLastAttempted();

        if ($user instanceof User && ! $user->isActive() && $guard->getProvider()->validateCredentials($user, $credentials)) {
            return [
                'status' => SecurityConst::LOGIN_INACTIVE,
                'message' => $user->inactiveMessage(),
            ];
        }

        return ['status' => SecurityConst::LOGIN_FAILED];
    }

    public function loginUser(Request $request, User $user): void
    {
        $this->sessionService->login($request, 'user', $user);
    }

    public function logout(Request $request): void
    {
        $this->sessionService->logout($request, 'user');
    }

    public function google(Request $request, SocialiteUser $googleUser): array
    {
        $googleId = (string) $googleUser->getId();
        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $emailVerified = filter_var(Arr::get((array) $googleUser->getRaw(), 'email_verified', false), FILTER_VALIDATE_BOOLEAN);

        if ($googleId === '' || $email === '') {
            return ['status' => SecurityConst::GOOGLE_FAILED];
        }

        try {
            $user = $this->userRepository->findBy($googleId, 'google_id');

            if ($user) {
                return $this->googleResult($user, SecurityConst::GOOGLE_LOGGED_IN);
            }

            $user = $this->userRepository->findByEmail($email);

            if ($user) {
                if (! $user->isActive()) {
                    return $this->googleResult($user, SecurityConst::GOOGLE_INACTIVE);
                }

                if (filled($user->google_id)) {
                    return ['status' => SecurityConst::GOOGLE_CONFLICT];
                }

                if (! $emailVerified) {
                    return ['status' => SecurityConst::GOOGLE_UNVERIFIED];
                }

                if (! $user->hasVerifiedEmail()) {
                    $request->session()->put(SecurityConst::SESSION_GOOGLE_LINK, [
                        'google_id' => $googleId,
                        'email' => $email,
                        'expires_at' => now()->addMinutes(SecurityConst::GOOGLE_LINK_TTL_MINUTES)->timestamp,
                    ]);

                    return ['status' => SecurityConst::GOOGLE_LINK_REQUIRED, 'email' => $user->email];
                }

                $this->linkGoogle($user, $googleId);

                return $this->googleResult($user, SecurityConst::GOOGLE_LINKED);
            }

            if (! $emailVerified) {
                return ['status' => SecurityConst::GOOGLE_UNVERIFIED];
            }

            $user = $this->userRepository->newQuery()->forceCreate([
                'fullname' => $googleUser->getName() ?: $email,
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
                'google_id' => $googleId,
                'email_verified_at' => now(),
            ]);

            return $this->googleResult($user, SecurityConst::GOOGLE_CREATED);
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);

            return ['status' => SecurityConst::GOOGLE_FAILED];
        }
    }

    public function sendResetLinkEmail(array $params): void
    {
        try {
            $status = Password::broker('users')->sendResetLink(['email' => $params['email']]);

            if ($status !== Password::RESET_LINK_SENT) {
                Log::info(__METHOD__, ['status' => $status]);
            }
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);
        }
    }

    public function reset(array $params): bool
    {
        try {
            $status = Password::broker('users')->reset(
                [
                    'email' => $params['email'],
                    'password' => $params['password'],
                    'password_confirmation' => $params['password_confirmation'] ?? $params['password'],
                    'token' => $params['token'],
                ],
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'email_verified_at' => $user->email_verified_at ?? now(),
                    ])->save();

                    $this->sessionService->terminateOtherSessions($user);

                    event(new PasswordReset($user));

                    $this->notifyPasswordChanged($user);
                }
            );

            return $status === Password::PASSWORD_RESET;
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);

            return false;
        }
    }

    public function notifyPasswordChanged(User $user): void
    {
        try {
            $user->notify(new PasswordChanged());
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);
        }
    }

    protected function completePendingGoogleLink(Request $request, User $user): bool
    {
        $pending = $request->session()->pull(SecurityConst::SESSION_GOOGLE_LINK);

        if (! is_array($pending) || (int) ($pending['expires_at'] ?? 0) < now()->timestamp) {
            return false;
        }

        if (strcasecmp((string) ($pending['email'] ?? ''), (string) $user->email) !== 0 || filled($user->google_id)) {
            return false;
        }

        if ($this->userRepository->findBy($pending['google_id'], 'google_id')) {
            return false;
        }

        $this->linkGoogle($user, (string) $pending['google_id']);

        return true;
    }

    protected function linkGoogle(User $user, string $googleId): void
    {
        $user->forceFill([
            'google_id' => $googleId,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        try {
            $user->notify(new GoogleAccountLinked());
        } catch (\Throwable $th) {
            $this->logError(__METHOD__, $th);
        }
    }

    protected function googleResult(User $user, string $status): array
    {
        if (! $user->isActive()) {
            return [
                'status' => SecurityConst::GOOGLE_INACTIVE,
                'message' => $user->inactiveMessage(),
            ];
        }

        return ['status' => $status, 'user' => $user];
    }

    protected function logError(string $method, \Throwable $th): void
    {
        Log::error($method, [
            'message' => $th->getMessage(),
            'file' => $th->getFile(),
            'line' => $th->getLine(),
        ]);
    }
}
