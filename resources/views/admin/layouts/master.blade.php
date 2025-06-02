<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>@yield('title', 'Dashboard - Alibubu')</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    @include('admin.layouts.partials.css')
</head>

<body>

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        @include('admin.layouts.partials.header')
    </header>
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        @include('admin.layouts.partials.aside')
    </aside>
    <!-- End Sidebar-->

    <!-- ======= Main ====== -->
    <main id="main" class="main">
        @yield('content')
    </main>
    <!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        @include('admin.layouts.partials.footer')
    </footer>
    <!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i>
    </a>

    @include('admin.layouts.partials.script')
    @stack('scripts')
</body>

</html>
