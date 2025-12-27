<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.email_final_summary_subject', ['location' => $request->location, 'date' => $request->target_date->format('Y-m-d')]) }}</title>
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
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }
        .highlight h2 {
            margin: 0 0 10px 0;
            color: #1e40af;
            font-size: 28px;
        }
        .provider-comparison {
            margin: 25px 0;
        }
        .provider-card {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
        }
        .provider-card h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 14px;
        }
        .stat-label {
            color: #6b7280;
        }
        .stat-value {
            font-weight: bold;
            color: #1f2937;
        }
        .forecast-change {
            background-color: #eff6ff;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-size: 13px;
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
        <h1>🎯 {{ __('app.email_final_summary_title') }}</h1>

        <p>{{ __('app.email_greeting') }}</p>

        <div class="highlight">
            <h2>{{ $request->location }}</h2>
            <p>{{ __('app.target_date') }}: <strong>{{ $request->target_date->format('Y-m-d') }}</strong></p>
            <p style="font-size: 14px; color: #6b7280;">{{ __('app.email_final_summary_completed') }}</p>
        </div>

        <p>{{ __('app.email_final_summary_intro') }}</p>

        <div class="provider-comparison">
            <h2 style="color: #1f2937; font-size: 18px;">{{ __('app.email_final_summary_comparison') }}</h2>

            @foreach($providerStats as $stat)
                <div class="provider-card">
                    <h3>{{ $stat['provider_name'] }}</h3>

                    <div class="stat-row">
                        <span class="stat-label">{{ __('app.email_final_summary_snapshots') }}:</span>
                        <span class="stat-value">{{ $stat['snapshot_count'] }}</span>
                    </div>

                    <div class="stat-row">
                        <span class="stat-label">{{ __('app.email_final_summary_avg_temp') }}:</span>
                        <span class="stat-value">{{ $stat['avg_temp'] }}°C</span>
                    </div>

                    <div class="stat-row">
                        <span class="stat-label">{{ __('app.email_final_summary_temp_range') }}:</span>
                        <span class="stat-value">{{ $stat['min_temp'] }}°C - {{ $stat['max_temp'] }}°C</span>
                    </div>

                    @if($stat['first_forecast'] && $stat['last_forecast'])
                        <div class="forecast-change">
                            <strong>{{ __('app.email_final_summary_first_vs_last') }}</strong><br>
                            {{ __('app.email_final_summary_first') }}: {{ round($stat['first_forecast']->avg_temp) }}°C, {{ $stat['first_forecast']->conditions }}<br>
                            {{ __('app.email_final_summary_last') }}: {{ round($stat['last_forecast']->avg_temp) }}°C, {{ $stat['last_forecast']->conditions }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if(count($providerStats) > 1)
            <p style="font-size: 13px; color: #6b7280; font-style: italic;">
                💡 {{ __('app.email_final_summary_tip') }}
            </p>
        @endif

        <div style="text-align: center; margin: 25px 0;">
            <a href="{{ route('requests.show', $request->id) }}" class="btn">
                {{ __('app.email_final_summary_view_full') }}
            </a>
        </div>

        <div class="footer">
            <p>{{ __('app.email_footer_line1') }}</p>
            <p>{{ __('app.email_final_summary_footer') }}</p>

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
