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

<body class="min-h-screen" data-success="{{ session('success') }}" data-error="{{ session('error') }}">
    @include('admin.layouts.partials.sidebar')

    <div id="content" class="flex flex-col">
        @include('admin.layouts.partials.header')

        <main class="flex-1 px-4 py-6 md:px-6 md:py-8">
            <div class="max-w-[1600px] mx-auto">
                @hasSection('breadcrumb')
                    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-5">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition-colors">
                            <i class="fa-solid fa-house"></i>
                        </a>
                        @yield('breadcrumb')
                    </nav>
                @endif

                @yield('content')
            </div>
        </main>

        @include('admin.layouts.partials.footer')
    </div>

    <button type="button" id="scrollToTop"
        class="fixed z-40 bottom-6 right-6 w-12 h-12 rounded-full bg-primary text-white shadow-lg hover:bg-primary-hover transition-all opacity-0 pointer-events-none"
        aria-label="Top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <script>
        window.confirmLabels = {
            yes: @json(__('common.confirm.yes')),
            no: @json(__('common.confirm.no')),
        };

        window.alertLabels = {
            success: @json(__('common.alerts.success')),
            error: @json(__('common.alerts.error')),
        };
    </script>

    @include('admin.layouts.partials.common.scripts')
    @stack('scripts')
</body>

</html>
