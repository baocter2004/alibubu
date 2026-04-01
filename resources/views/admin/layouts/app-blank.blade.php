<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Alibubu - Your Shop')</title>
    @include('admin.layouts.partials.common.css')
    @stack('styles')
</head>

<body class="bg-background-light" data-success="{{ session('success') }}" data-error="{{ session('error') }}">
    <main class="max-w-7xl flex justify-center items-center mx-auto h-full px-4 py-8">
        @yield('content')
    </main>
    @include('admin.layouts.partials.common.scripts')

    @stack('scripts')
</body>

</html>
