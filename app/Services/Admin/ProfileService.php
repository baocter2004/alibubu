<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Notifications\EmailChanged;
use App\Notifications\PasswordChanged;
use App\Services\Auth\SessionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProfileService
{
    public function __construct(protected SessionService $sessionService) {}

    public function updateProfile(Admin $admin, array $params): Admin
    {
        $oldEmail = $admin->email;
        $emailChanged = $oldEmail !== $params['email'];

        $admin->update([
            'name' => $params['name'],
            'email' => $params['email'],
        ]);

        if ($emailChanged) {
            $this->notifyEmailChanged($oldEmail, $admin);
        }

        return $admin->refresh();
    }

    public function updatePassword(Admin $admin, string $password): void
    {
        $admin->forceFill(['password' => Hash::make($password)])->save();

        $this->sessionService->terminateOtherSessions($admin);

        $this->notifyPasswordChanged($admin);
    }

    protected function notifyEmailChanged(string $oldEmail, Admin $admin): void
    {
        try {
            Notification::route('mail', $oldEmail)
                ->notify((new EmailChanged($admin->email))->locale(config('app.locale')));
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }

    protected function notifyPasswordChanged(Admin $admin): void
    {
        try {
            $admin->notify((new PasswordChanged())->locale(config('app.locale')));
        } catch (\Throwable $th) {
            Log::error(__METHOD__, [
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
            ]);
        }
    }
}
