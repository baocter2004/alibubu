<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('common.app_name'))</title>
    <meta name="description" content="@yield('meta_description', __('client.home.description'))">
    <meta property="og:title" content="@yield('title', __('common.app_name'))">
    <meta property="og:description" content="@yield('meta_description', __('client.home.description'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="theme-color" content="#14284B">

    <script>document.documentElement.classList.add('js-reveal');</script>

    @include('client.layouts.partials.common.css')
    @stack('styles')
</head>

<body class="bg-background-light" data-success="{{ session('success') }}" data-error="{{ session('error') }}">
    <a href="#main" class="skip-link">{{ __('client.nav.skip_to_content') }}</a>

    @include('client.layouts.partials.header')

    @include('client.layouts.partials.sidebar')

    <main id="main" class="max-w-7xl mx-auto px-4 py-6 md:py-10 pb-24 md:pb-12">
        @yield('content')
    </main>

    @include('client.layouts.partials.footer')
    @include('client.layouts.partials.bottom-nav')

    <script>
        window.alertLabels = {
            success: @json(__('common.alerts.success')),
            error: @json(__('common.alerts.error')),
        };
    </script>

    @include('client.layouts.partials.common.scripts')

    @stack('scripts')
</body>

</html>
