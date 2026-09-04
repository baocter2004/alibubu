<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('common.app_name')) · {{ __('admin/nav.brand_subtitle') }}</title>
    @include('admin.layouts.partials.common.css')
    @stack('styles')
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-primary-soft"
    data-success="{{ session('success') }}" data-error="{{ session('error') }}">
    <div class="absolute top-4 right-4 z-10">
        @include('components.locale-switcher')
    </div>

    <main class="min-h-screen flex items-center justify-center px-4 py-10">
        @yield('content')
    </main>

    <script>
        window.alertLabels = {
            success: @json(__('common.alerts.success')),
            error: @json(__('common.alerts.error')),
        };
    </script>

    @include('admin.layouts.partials.common.scripts')
    @stack('scripts')
</body>

</html>
