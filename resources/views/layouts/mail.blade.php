<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject')</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #f8fafc;
            padding: 40px 20px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }
        .logo-box {
            width: 60px;
            height: 60px;
            line-height: 60px;
            background-color: #2563eb;
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            border-radius: 12px;
            margin: 0 auto 16px;
            text-align: center;
        }
        .brand-name {
            color: #1e293b;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px;
            color: #334155;
            line-height: 1.6;
        }
        .content h2 {
            color: #1e293b;
            margin-top: 0;
            font-size: 16px; /* Normal text size as requested */
            font-weight: 600;
            margin-bottom: 24px;
        }
        .footer {
            padding: 40px 30px;
            text-align: center;
            background-color: #ffffff;
            border-top: 1px solid #f1f5f9;
        }
        .social-icons {
            margin-bottom: 24px;
        }
        .social-icon {
            display: inline-block;
            margin: 0 8px;
            width: 32px;
            height: 32px;
            line-height: 32px;
            background-color: #f1f5f9;
            border-radius: 50%;
            color: #475569;
            text-decoration: none;
        }
        .footer-text {
            font-size: 13px;
            color: #64748b;
            line-height: 1.8;
        }
        .button-container {
            margin: 35px 0;
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
            font-size: 15px;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.1);
        }
        .divider {
            height: 1px;
            background-color: #f1f5f9;
            margin: 35px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <div class="logo-box">FAS</div>
                <h1 class="brand-name">{{ config('app.name') }}</h1>
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
                <p style="margin-bottom: 0; font-size: 14px;">
                    @yield('outro')<br>
                    <strong>The {{ config('app.name') }} Team</strong>
                </p>
            </div>
            <div class="footer">
                <div class="social-icons">
                    <a href="#" class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="16" alt="Facebook"></a>
                    <a href="#" class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="16" alt="Twitter"></a>
                    <a href="#" class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/733/733558.png" width="16" alt="Instagram"></a>
                    <a href="#" class="social-icon"><img src="https://cdn-icons-png.flaticon.com/512/3536/3536445.png" width="16" alt="LinkedIn"></a>
                </div>
                <div class="footer-text">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                    If you have any questions, please contact our support team.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
