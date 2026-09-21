<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifyUserEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public string $verificationUrl;
    public int $expireMinutes;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->expireMinutes = (int) config('auth.verification.expire', 60);
        $this->verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes($this->expireMinutes),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('client_auth.mail.verify.title'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'components.mails.verify-email',
            with: ['expireMinutes' => $this->expireMinutes],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
