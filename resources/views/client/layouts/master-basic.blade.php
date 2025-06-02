<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('client.layouts.partials.css')
</head>

<body>
    <div class="site-wrap">
        <div class="mt-6 mb-2">
            @yield('content')
        </div>
    </div>

    @include('client.layouts.partials.script')
    @stack('scripts')

</body>

</html>
