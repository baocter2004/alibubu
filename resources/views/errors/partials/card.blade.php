<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __("errors.{$code}.title") }} · {{ __('common.app_name') }}</title>
    <style>
        :root {
            color-scheme: light;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f6fb;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 45px -20px rgba(20, 40, 75, 0.35);
            padding: 40px 32px;
            text-align: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: hsl(218 58% 19%);
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px;
            color: hsl(218 58% 19%);
        }

        p {
            margin: 0 0 24px;
            font-size: 14px;
            line-height: 1.6;
            color: #4b5563;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: opacity .15s ease;
        }

        .btn:hover {
            opacity: .88;
        }

        .btn-primary {
            background: hsl(218 58% 19%);
            color: #fff;
        }

        .subtitle {
            display: block;
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 6px;
        }
    </style>
</head>

<body>
    <div class="card">
        <span class="badge">{{ $code }}</span>
        <span class="subtitle">{{ $variant === 'admin' ? __('common.app_name') . ' · Admin' : __('common.app_name') }}</span>
        <h1>{{ __("errors.{$code}.heading") }}</h1>
        <p>{{ __("errors.{$code}.message") }}</p>

        <div class="actions">
            <a href="{{ $homeUrl }}" class="btn btn-primary">{{ __('errors.actions.back_home') }}</a>
        </div>
    </div>
</body>

</html>
