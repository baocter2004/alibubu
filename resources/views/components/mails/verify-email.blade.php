@extends('components.mails.layouts')

@section('title', __('client_auth.mail.verify.title'))

@section('content')
    <p>{{ __('client_auth.mail.verify.greeting', ['name' => $user->fullname]) }}</p>

    <p>{{ __('client_auth.mail.verify.intro') }}</p>

    <div style="text-align:center; margin:24px 0;">
        <a href="{{ $verificationUrl }}"
            style="background:#2563eb;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;display:inline-block;">
            {{ __('client_auth.mail.verify.action') }}
        </a>
    </div>

    <p style="font-size:12px; color:#888;">
        {{ __('client_auth.mail.verify.expires', ['minutes' => 60]) }}
    </p>

    <p style="font-size:12px; color:#888;">
        {{ __('client_auth.mail.verify.ignore') }}
    </p>
@endsection
