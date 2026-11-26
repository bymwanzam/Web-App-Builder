<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            line-height: 1.6;
        }
        .email-wrapper {
            padding: 40px 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #1d4ed8;
            padding: 30px;
            text-align: center;
        }
        .header-logo {
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px 30px;
        }
        .message-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 20px;
            line-height: 1.3;
        }
        .divider {
            height: 3px;
            background: linear-gradient(90deg, #1d4ed8 0%, #3b82f6 100%);
            margin: 0 0 25px;
            border-radius: 2px;
        }
        .message-body {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.7;
        }
        .message-body p {
            margin: 0 0 15px;
        }
        .message-body strong {
            color: #1f2937;
        }
        .message-body a {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
        }
        .message-body a:hover {
            text-decoration: underline;
        }
        .message-body ul,
        .message-body ol {
            margin: 15px 0;
            padding-left: 25px;
        }
        .message-body li {
            margin-bottom: 8px;
        }
        .info-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }
        .info-box strong {
            color: #1e40af;
        }
        .warning-box {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .error-box {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .success-box {
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background-color: #1d4ed8;
            color: #ffffff !important;
            text-decoration: none !important;
            font-weight: 600;
            font-size: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background-color: #1e40af;
        }
        .btn-secondary {
            background-color: #6b7280;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        .btn-success {
            background-color: #059669;
        }
        .btn-success:hover {
            background-color: #047857;
        }
        .btn-danger {
            background-color: #dc2626;
        }
        .btn-danger:hover {
            background-color: #b91c1c;
        }
        .footer {
            background-color: #f9fafb;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            color: #9ca3af;
            font-size: 13px;
        }
        .footer a {
            color: #6b7280;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .text-muted {
            color: #9ca3af;
            font-size: 13px;
        }
        .text-center {
            text-align: center;
        }
        .mt-4 {
            margin-top: 20px;
        }
        .mb-4 {
            margin-bottom: 20px;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 20px 10px;
            }
            .email-container {
                border-radius: 8px;
            }
            .header {
                padding: 20px;
            }
            .header-logo {
                font-size: 24px;
            }
            .content {
                padding: 30px 20px;
            }
            .message-title {
                font-size: 20px;
            }
            .message-body {
                font-size: 14px;
            }
            .btn {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="header">
                <h1 class="header-logo">{{ $appName ?? config('app.name') }}</h1>
            </div>

            <!-- Content -->
            <div class="content">
                @hasSection('title')
                    <h2 class="message-title">@yield('title')</h2>
                    <div class="divider"></div>
                @endif

                <div class="message-body">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ $appName ?? config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
