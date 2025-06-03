@extends('common.emails.base')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-semibold mb-6">Xác minh thành công</h2>

        @if (session('status'))
            <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        <p>Tài khoản của bạn đã được xác minh email thành công!</p>
        <a href="{{ route('index') }}" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded">
            Về trang chủ
        </a>
    </div>
@endsection
