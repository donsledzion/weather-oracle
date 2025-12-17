<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.email_verify_title') }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .info-box {
            background: white;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #667eea;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px 5px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
        }
        .button-primary {
            background: #667eea;
            color: white;
        }
        .button-secondary {
            background: #10b981;
            color: white;
        }
        .button-danger {
            background: #ef4444;
            color: white;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🌤️ Weather Oracle</h1>
        <p>{{ __('app.email_verify_title') }}</p>
    </div>

    <div class="content">
        <p>{{ __('app.email_greeting') }}</p>

        <p>{{ __('app.email_requested_monitoring') }}</p>

        <div class="info-box">
            <strong>📍 {{ __('app.location') }}:</strong> {{ $request->location }}<br>
            <strong>📅 {{ __('app.target_date') }}:</strong> {{ $request->target_date->format('F j, Y') }}<br>
            <strong>📧 {{ __('app.email') }}:</strong> {{ $request->email }}
        </div>

        <p><strong>{{ __('app.email_please_verify') }}</strong></p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $verifyUrl }}" class="button button-primary">
                ✅ {{ __('app.email_activate_button') }}
            </a>
        </div>

        <p style="text-align: center; color: #6b7280; font-size: 0.875rem;">
            {{ __('app.email_or_visit_dashboard') }}
        </p>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ $dashboardUrl }}" class="button button-secondary">
                📊 {{ __('app.email_view_dashboard') }}
            </a>
        </div>

        <div class="warning">
            <strong>⏰ {{ __('app.important') }}:</strong> {{ __('app.email_expires_warning') }}
        </div>

        <p style="text-align: center; margin-top: 30px;">
            <small>{{ __('app.email_not_interested') }}
                <a href="{{ $rejectUrl }}" style="color: #ef4444;">{{ __('app.email_cancel_request') }}</a>
            </small>
        </p>
    </div>

    <div class="footer">
        <p>{{ __('app.email_footer_line1') }}<br>
        {{ __('app.email_footer_line2') }}</p>

        <p style="font-size: 0.75rem; color: #9ca3af;">
            {{ __('app.email_footer_ignore') }}
        </p>
    </div>
</body>
</html>
