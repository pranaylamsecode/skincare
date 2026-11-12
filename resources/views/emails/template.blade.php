<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailSubject }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fff8f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #2b1514;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #f1e0dc;
            box-shadow: 0 4px 15px rgba(122, 46, 46, 0.05);
        }
        .header {
            background-color: #7a2e2e;
            color: #ffffff;
            padding: 24px 30px;
            text-align: center;
        }
        .logo-box {
            display: inline-block;
            background: #ffffff;
            padding: 8px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
            margin-bottom: 8px;
        }
        .logo-box img {
            max-height: 48px;
            max-width: 220px;
            height: auto;
            width: auto;
            display: block;
            object-fit: contain;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 13px;
            color: #fbeded;
            letter-spacing: 0.5px;
        }
        .body {
            padding: 32px 30px;
            font-size: 15px;
            line-height: 1.7;
            color: #2b1514;
            white-space: pre-line;
        }
        .footer {
            background-color: #fff8f5;
            border-top: 1px solid #f1e0dc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #5e4a48;
        }
        .btn {
            display: inline-block;
            background-color: #b60a0a;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(!empty($logoUrl))
                <div class="logo-box">
                    <a href="{{ url('/') }}" target="_blank" style="text-decoration: none; display: block;">
                        <img src="{{ $logoUrl }}" alt="{{ $siteName ?? 'FMZ Glow' }}" />
                    </a>
                </div>
            @else
                <h1 style="margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 1px;">{{ $siteName ?? 'FMZ GLOW' }}</h1>
            @endif
            <p>At-Home Skincare by FMZ India</p>
        </div>
        <div class="body">
{{ $bodyContent }}
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} FMZ India. All rights reserved.</p>
            <p>Questions? Contact us on WhatsApp or reply to this email.</p>
        </div>
    </div>
</body>
</html>
