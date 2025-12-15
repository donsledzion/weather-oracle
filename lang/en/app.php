<?php

return [
    // Page titles
    'page_title' => 'Weather Oracle',
    'monitor_forecasts' => 'Monitor Weather Forecasts',

    // Form labels
    'location' => 'Location',
    'location_placeholder' => 'Enter city name or coordinates',
    'target_date' => 'Target Date',
    'email' => 'Email (optional)',
    'email_placeholder' => 'your@email.com',
    'start_monitoring' => 'Start Monitoring',
    'creating' => 'Creating...',

    // Messages
    'request_created_success' => 'Monitoring request created and initial forecast fetched successfully!',
    'request_created_no_data' => 'Monitoring request created. Forecast data will be available when target date is within 5 days.',

    // Errors
    'location_not_found' => 'Location not found. Please check the spelling or try coordinates (lat,lon).',
    'api_config_error' => 'Weather API configuration error. Please contact support.',
    'fetch_failed' => 'Failed to fetch weather data: :message',

    // Validation
    'validation' => [
        'location_required' => 'Location is required.',
        'location_min' => 'Location must be at least 2 characters.',
        'target_date_required' => 'Target date is required.',
        'target_date_after' => 'Target date must be in the future.',
        'email_invalid' => 'Please provide a valid email address.',
    ],

    // Request details
    'request_details' => 'Request Details',
    'status' => 'Status',
    'created' => 'Created',
    'active' => 'Active',
    'completed' => 'Completed',

    // Forecast snapshots
    'forecast_snapshots' => 'Forecast Snapshots',
    'no_data_yet' => 'No forecast data available yet',
    'no_data_message' => 'Target date is too far in the future. Weather forecasts are available up to 5 days in advance. Forecast data will start appearing when your target date is within range.',
    'fetched' => 'Fetched',
    'forecast_for' => 'Forecast for',

    // Weather data
    'temperature' => 'Temperature',
    'feels_like' => 'Feels like',
    'min' => 'Min',
    'max' => 'Max',
    'conditions' => 'Conditions',
    'precipitation' => 'Precipitation',
    'humidity' => 'Humidity',
    'pressure' => 'Pressure',
    'wind' => 'Wind',
    'clouds' => 'Clouds',

    // Chart
    'temperature_trends' => 'Temperature Trends',
    'avg_temperature' => 'Avg Temperature',
    'min_temperature' => 'Min Temperature',
    'max_temperature' => 'Max Temperature',
    'not_enough_data' => 'Not enough data for chart (need at least 2 snapshots)',

    // Monitoring list
    'your_monitoring_requests' => 'Your Monitoring Requests',
    'no_requests_yet' => 'No monitoring requests yet.',
    'view_details' => 'View Details',
    'snapshots_count' => ':count snapshot(s)',
];
