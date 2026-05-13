<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject')</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f4f7fa;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1e293b;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px;
            color: #334155;
            line-height: 1.6;
        }
        .content h2 {
            color: #1e293b;
            margin-top: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .footer {
            padding: 30px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            background-color: #f8fafc;
        }
        .button-container {
            margin: 30px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #2563eb;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.2s;
        }
        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h1>{{ config('app.name') }}</h1>
            </div>
            <div class="content">
                <h2>@yield('intro')</h2>
                @yield('content')
                
                @if(isset($cta_link) && isset($cta_content))
                    <div class="button-container">
                        <a href="{{ $cta_link }}" class="button">{{ $cta_content }}</a>
                    </div>
                @endif

                <div class="divider"></div>
                <p style="margin-bottom: 0;">
                    @yield('outro')<br>
                    <strong>The {{ config('app.name') }} Team</strong>
                </p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                If you did not expect this email, you can safely ignore it.
            </div>
        </div>
    </div>
</body>
</html>
