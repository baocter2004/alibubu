<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield(section: 'title')</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .wrapper {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #0d6efd;
            padding: 16px;
            text-align: center;
            color: #ffffff;
        }

        .header img {
            max-height: 40px;
            margin-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: normal;
        }

        .content {
            padding: 24px;
            color: #333333;
            font-size: 14px;
            line-height: 1.5;
        }

        .content a.button {
            display: inline-block;
            background-color: #0d6efd;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 4px;
            margin-top: 16px;
        }

        .footer {
            background-color: #f1f1f1;
            padding: 12px;
            text-align: center;
            font-size: 12px;
            color: #777777;
        }

        .footer a {
            color: #0d6efd;
            text-decoration: none;
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="wrapper">
        <div class="header">
            @if (!empty($logoUrl))
                <img src="{{ $logoUrl }}" alt="Logo">
            @endif
            <h1>@yield('header_title', 'Alibubu')</h1>
        </div>

        <div class="content">
            @yield('content')
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Alibubu. Mọi quyền được bảo lưu.
            <br>
            <a href="{{ $footerLink ?? url('/') }}">Trang chủ</a>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
