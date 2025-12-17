<?php

return [
    // Page titles
    'page_title' => 'Weather Oracle',
    'monitor_forecasts' => 'Monitor Weather Forecasts',

    // Form labels
    'location' => 'Location',
    'location_placeholder' => 'Enter city name or coordinates',
    'target_date' => 'Target Date',
    'email' => 'Email',
    'email_placeholder' => 'your@email.com',
    'start_monitoring' => 'Start Monitoring',
    'creating' => 'Creating...',

    // Messages
    'request_created_success' => 'Monitoring request created and initial forecast fetched successfully!',
    'request_created_no_data' => 'Monitoring request created. Forecast data will be available when target date is within 5 days.',
    'request_created_verify_email' => 'Check your email inbox and click the activation link to start monitoring!',
    'request_created_no_email' => 'Monitoring request created! Forecasts will be fetched automatically.',
    'guest_limit_reached' => 'You have reached the limit of 5 requests. Activate or reject pending requests or wait for active ones to expire.',

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
    'back_to_all_requests' => 'Back to all requests',
    'snapshots' => 'snapshots',

    // Guest dashboard
    'guest_dashboard' => 'Your Dashboard',
    'managing_requests_for' => 'Managing requests for',
    'guest_dashboard_info' => 'You can activate pending requests, view active monitoring, and check archives.',
    'pending_verification' => 'Pending Verification',
    'expired' => 'Expired',
    'rejected' => 'Rejected',
    'pending_activation_message' => 'This request is waiting for activation. Click the button below to start monitoring.',
    'expires_in' => 'Expires in',
    'activate_now' => 'Activate Now',
    'cancel_request' => 'Cancel',
    'confirm_reject' => 'Are you sure you want to cancel this request?',
    'want_more' => 'Want More?',
    'guest_limit_info' => 'As a guest, you can have up to 5 active requests. Create a free account to increase the limit to 20 requests!',
    'create_free_account' => 'Create Free Account',

    // Email verification
    'important' => 'Important',
    'email_verify_title' => 'Verify Your Monitoring Request',
    'email_greeting' => 'Hello!',
    'email_requested_monitoring' => 'You\'ve requested to monitor weather forecasts for:',
    'email_please_verify' => 'Please verify your request to start monitoring:',
    'email_activate_button' => 'Activate Monitoring',
    'email_or_visit_dashboard' => 'Or visit your dashboard to manage all your requests:',
    'email_view_dashboard' => 'View Dashboard',
    'email_expires_warning' => 'This verification link will expire in 2 hours. If you don\'t verify within this time, your request will be automatically cancelled.',
    'email_not_interested' => 'Not interested?',
    'email_cancel_request' => 'Cancel this request',
    'email_footer_line1' => 'This is an automated email from Weather Oracle.',
    'email_footer_line2' => 'You received this because you requested weather forecast monitoring.',
    'email_footer_ignore' => 'If you didn\'t request this, you can safely ignore this email.',
];
