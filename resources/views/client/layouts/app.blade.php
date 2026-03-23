<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Alibubu - Your Shop')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-light-bg">
    @include('client.layouts.partials.header')

    @include('client.layouts.partials.sidebar')

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    @include('client.layouts.partials.footer')

    @stack('scripts')
</body>

</html>
