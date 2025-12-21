<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.email_first_snapshot_subject', ['location' => $request->location, 'provider' => $providerName]) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .highlight {
            background-color: #dbeafe;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .weather-data {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
        }
        .weather-data p {
            margin: 8px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
        }
        .btn-secondary {
            background-color: #6b7280;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        .unsubscribe {
            color: #9ca3af;
            font-size: 11px;
            margin-top: 15px;
        }
        .unsubscribe a {
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🌤️ {{ __('app.email_first_snapshot_title') }}</h1>

        <p>{{ __('app.email_greeting') }}</p>

        <div class="highlight">
            <p><strong>{{ __('app.email_first_snapshot_intro', ['provider' => $providerName]) }}</strong></p>
            <p>📍 <strong>{{ $request->location }}</strong></p>
            <p>📅 {{ __('app.target_date') }}: <strong>{{ $request->target_date->format('Y-m-d') }}</strong></p>
        </div>

        <div class="weather-data">
            <h3>{{ __('app.forecast_for') }} {{ $snapshot->forecast_date->format('Y-m-d') }}</h3>
            <p>🌡️ {{ __('app.temperature') }}: <strong>{{ round($snapshot->avg_temp) }}°C</strong> ({{ round($snapshot->min_temp) }}°C - {{ round($snapshot->max_temp) }}°C)</p>
            <p>🌤️ {{ __('app.conditions') }}: <strong>{{ $snapshot->conditions }}</strong></p>
            @if($snapshot->precipitation_mm > 0)
                <p>🌧️ {{ __('app.precipitation') }}: <strong>{{ $snapshot->precipitation_mm }} mm</strong></p>
            @endif
            <p>💨 {{ __('app.wind') }}: <strong>{{ round($snapshot->wind_speed) }} km/h</strong></p>
            <p>💧 {{ __('app.humidity') }}: <strong>{{ $snapshot->humidity }}%</strong></p>
        </div>

        <p>{{ __('app.email_first_snapshot_message') }}</p>

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ route('request.details', $request->id) }}" class="btn">
                {{ __('app.view_details') }}
            </a>
        </div>

        <div class="footer">
            <p>{{ __('app.email_footer_line1') }}</p>
            <p>{{ __('app.email_footer_line2') }}</p>

            <div class="unsubscribe">
                <p>
                    {{ __('app.email_notification_settings') }}:
                    <a href="{{ route('notifications.show', $notificationToken) }}">{{ __('app.notification_preferences') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
