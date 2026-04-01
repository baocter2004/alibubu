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

<body data-success="{{ session('success') }}" data-error="{{ session('error') }}">

    @include('admin.layouts.partials.header')

    <div class="flex min-h-screen bg-gray-50">
        @include('admin.layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            @include('admin.layouts.partials.footer')
        </div>

        @include('admin.layouts.partials.common.scripts')
    </div>
    @stack('scripts')
</body>

</html>
