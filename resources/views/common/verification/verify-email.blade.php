@extends('common.emails.base')

@section('title', 'Xác minh địa chỉ email')
@section('header_title', 'Xác minh địa chỉ email')

@section('content')
    <p>Xin chào {{ $userName ?? 'Bạn' }},</p>
    <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>{{ config('app.name') }}</strong>.</p>
    <p>Vui lòng nhấn nút bên dưới để xác minh địa chỉ email của bạn:</p>
    <p style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="button">Xác minh Email</a>
    </p>
    <p>Liên kết sẽ hết hạn sau {{ $expires ?? 60 }} phút.</p>
    <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email.</p>
@endsection
