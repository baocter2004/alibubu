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

<body class="flex min-h-screen bg-background-light" data-success="{{ session('success') }}" data-error="{{ session('error') }}">
    @include('admin.layouts.partials.sidebar')

    <div id="content" class="flex flex-col flex-1">
        @include('admin.layouts.partials.header')

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            <div class="w-full mx-auto">
                @yield('content')
            </div>

            <button id="scrollToTop"
                class="fixed z-20 bottom-6 right-6 p-3 w-12 h-12 rounded-full bg-blue-500 text-white shadow-lg hover:bg-blue-600 transition opacity-0 pointer-events-none">
                <i class="fa-solid fa-arrow-up"></i>
            </button>
        </main>

        @include('admin.layouts.partials.footer')

        @include('admin.layouts.partials.common.scripts')
    </div>
    @stack('scripts')
</body>

</html>
