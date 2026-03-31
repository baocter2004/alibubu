@extends('components.mails.layouts')

@section('title', 'Xác minh email')

@section('content')

    <p>Xin chào {{ $user->name }},</p>

    <p>Nhấn nút bên dưới để xác minh tài khoản:</p>

    <div style="text-align:center; margin:20px 0;">
        <a href="{{ $verificationUrl }}"
            style="background:#2563eb;color:#fff;padding:12px 20px;border-radius:6px;text-decoration:none;">
            Xác minh email
        </a>
    </div>

    <p style="font-size:12px; color:#888;">
        Link sẽ hết hạn sau 60 phút.
    </p>

@endsection
