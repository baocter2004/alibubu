<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Alibubu - Your Shop')</title>
    @include('client.layouts.partials.common.css')
    @stack('styles')
</head>

<body class="bg-light-bg">
    @include('client.layouts.partials.header')

    @include('client.layouts.partials.sidebar')

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    @include('client.layouts.partials.footer')
    @include('client.layouts.partials.common.scripts')
    
    @stack('scripts')
</body>

</html>
