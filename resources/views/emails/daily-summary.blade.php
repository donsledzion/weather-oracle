<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.email_daily_summary_subject', ['count' => $requests->count()]) }}</title>
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
        .summary-box {
            background-color: #dbeafe;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .summary-box h2 {
            margin: 0;
            color: #1e40af;
            font-size: 32px;
        }
        .summary-box p {
            margin: 5px 0 0 0;
            color: #1e40af;
        }
        .request-item {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #2563eb;
        }
        .request-item h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
        }
        .request-item p {
            margin: 5px 0;
            font-size: 14px;
            color: #6b7280;
        }
        .snapshot-info {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 8px;
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
        <h1>📊 {{ __('app.email_daily_summary_title') }}</h1>

        <p>{{ __('app.email_greeting') }}</p>

        <div class="summary-box">
            <h2>{{ $totalSnapshots }}</h2>
            <p>{{ __('app.email_daily_summary_new_snapshots') }}</p>
        </div>

        <p>{{ __('app.email_daily_summary_intro', ['count' => $requests->count()]) }}</p>

        @foreach($requests as $request)
            @php
                $todaySnapshots = $request->forecastSnapshots()->whereDate('created_at', today())->get();
                $latestSnapshot = $request->forecastSnapshots()->latest()->first();
            @endphp

            <div class="request-item">
                <h3>📍 {{ $request->location }}</h3>
                <p>📅 {{ __('app.target_date') }}: <strong>{{ $request->target_date->format('Y-m-d') }}</strong></p>

                @if($latestSnapshot)
                    <p>🌡️ {{ __('app.temperature') }}: <strong>{{ round($latestSnapshot->avg_temp) }}°C</strong></p>
                    <p>🌤️ {{ __('app.conditions') }}: <strong>{{ $latestSnapshot->conditions }}</strong></p>
                @endif

                <div class="snapshot-info">
                    {{ __('app.email_daily_summary_snapshots_today', ['count' => $todaySnapshots->count()]) }}
                    • {{ __('app.email_daily_summary_total_snapshots', ['count' => $request->forecastSnapshots()->count()]) }}
                </div>

                <div style="margin-top: 10px;">
                    <a href="{{ route('requests.show', $request->id) }}" style="color: #2563eb; text-decoration: none; font-size: 14px;">
                        {{ __('app.view_details') }} →
                    </a>
                </div>
            </div>
        @endforeach

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ route('dashboard') }}" class="btn">
                {{ __('app.email_view_dashboard') }}
            </a>
        </div>

        <div class="footer">
            <p>{{ __('app.email_footer_line1') }}</p>
            <p>{{ __('app.email_daily_summary_footer') }}</p>

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
