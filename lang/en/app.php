<?php

return [
    // Page titles
    'page_title' => 'Weather Oracle',
    'monitor_forecasts' => 'Monitor Weather Forecasts',
    'dashboard' => 'Dashboard',
    'login' => 'Login',
    'register' => 'Register',
    'logout' => 'Logout',

    // Form labels
    'location' => 'Location',
    'location_placeholder' => 'Enter city name or coordinates',
    'target_date' => 'Target Date',
    'email' => 'Email',
    'email_placeholder' => 'your@email.com',
    'start_monitoring' => 'Start Monitoring',
    'creating' => 'Creating...',

    // Messages
    'request_created_success' => 'Weather reading created and initial forecast fetched successfully!',
    'request_created_no_data' => 'Weather reading created. Forecast data will be available when target date is within 5 days.',
    'request_created_verify_email' => 'Check your email inbox and click the activation link to start monitoring!',
    'request_created_no_email' => 'Weather reading created! Forecasts will be fetched automatically.',
    'guest_limit_reached' => 'You have reached the limit of 5 weather readings. Activate or reject pending readings or wait for active ones to expire.',
    'user_limit_reached' => 'You have reached the limit of 20 active weather readings. Wait for one to expire or delete unnecessary ones.',
    'request_deleted' => 'Weather reading deleted successfully.',

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
    'request_details' => 'Reading Details',
    'status' => 'Status',
    'created' => 'Created',
    'active' => 'Active',
    'completed' => 'Completed',

    // Status labels
    'status_pending_verification' => 'Pending Verification',
    'status_verified' => 'Verified',
    'status_active' => 'Active',
    'status_completed' => 'Completed',
    'status_expired' => 'Expired',
    'status_rejected' => 'Rejected',

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
    'your_monitoring_requests' => 'Your Weather Readings',
    'no_requests_yet' => 'No weather readings yet.',
    'view_details' => 'View Details',
    'delete_request' => 'Delete',
    'confirm_delete' => 'Are you sure you want to delete this reading?',
    'snapshots_count' => ':count snapshot(s)',
    'back_to_all_requests' => 'Back to all readings',
    'snapshots' => 'snapshots',

    // Guest dashboard
    'guest_dashboard' => 'Your Dashboard',
    'managing_requests_for' => 'Managing readings for',
    'guest_dashboard_info' => 'You can activate pending readings, view active monitoring, and check archives.',
    'pending_verification' => 'Pending Verification',
    'expired' => 'Expired',
    'rejected' => 'Rejected',
    'pending_activation_message' => 'This reading is waiting for activation. Click the button below to start monitoring.',
    'expires_in' => 'Expires in',
    'activate_now' => 'Activate Now',
    'cancel_request' => 'Cancel',
    'confirm_reject' => 'Are you sure you want to cancel this reading?',
    'want_more' => 'Want More?',
    'guest_limit_info' => 'As a guest, you can have up to 5 active readings. Create a free account to increase the limit to 20 readings!',
    'create_free_account' => 'Create Free Account',

    // Email verification
    'important' => 'Important',
    'email_verify_title' => 'Confirm Your Weather Reading',
    'email_greeting' => 'Hello!',
    'email_requested_monitoring' => 'You\'ve created a weather reading for:',
    'email_please_verify' => 'Please confirm your reading to start monitoring:',
    'email_activate_button' => 'Activate Monitoring',
    'email_or_visit_dashboard' => 'Or visit your dashboard to manage all your readings:',
    'email_view_dashboard' => 'View Dashboard',
    'email_expires_warning' => 'This verification link will expire in 2 hours. If you don\'t confirm within this time, your reading will be automatically cancelled.',
    'email_not_interested' => 'Not interested?',
    'email_cancel_request' => 'Cancel this reading',
    'email_footer_line1' => 'This is an automated email from Weather Oracle.',
    'email_footer_line2' => 'You received this because you created a weather reading.',
    'email_footer_ignore' => 'If you didn\'t create this reading, you can safely ignore this email.',

    // Notification preferences
    'notification_preferences' => 'Notification Preferences',
    'managing_notifications_for' => 'Managing notifications for',
    'global_notification_settings' => 'Global Notification Settings',
    'global_settings_description' => 'These settings apply to all your weather readings. You can additionally enable/disable notifications for individual readings below.',
    'first_snapshot_notifications' => 'First Snapshot',
    'first_snapshot_description' => 'Notification when the first forecast appears for a given provider',
    'daily_summary_notifications' => 'Daily Summary',
    'daily_summary_description' => 'Daily summary of forecast changes',
    'final_summary_notifications' => 'Final Summary',
    'final_summary_description' => 'Summary of the reading after reaching the target date',
    'save_preferences' => 'Save Preferences',
    'preferences_updated' => 'Preferences updated successfully.',
    'per_request_settings' => 'Per-Reading Notifications',
    'per_request_description' => 'Enable or disable notifications for each reading individually. Notifications will only be sent if the global notification type is enabled.',
    'no_requests_found' => 'No readings found.',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',

    // First snapshot notification
    'email_first_snapshot_subject' => 'First forecast for :location from :provider',
    'email_first_snapshot_title' => 'First Forecast Available!',
    'email_first_snapshot_intro' => 'We just received the first forecast from :provider for your weather reading.',
    'email_first_snapshot_message' => 'We will check for forecast updates daily and notify you about important changes.',
    'email_notification_settings' => 'Manage notification settings',

    // Daily summary notification
    'email_daily_summary_subject' => 'Daily summary of your :count readings',
    'email_daily_summary_title' => 'Daily Weather Summary',
    'email_daily_summary_new_snapshots' => 'new forecasts today',
    'email_daily_summary_intro' => 'Here\'s a summary of your :count active weather readings:',
    'email_daily_summary_snapshots_today' => ':count new today',
    'email_daily_summary_total_snapshots' => ':count total',
    'email_daily_summary_footer' => 'You\'re receiving this email because you have daily summaries enabled.',

    // Final summary notification
    'email_final_summary_subject' => 'Weather reading summary: :location (:date)',
    'email_final_summary_title' => 'Your weather reading has completed!',
    'email_final_summary_completed' => 'Target date has been reached',
    'email_final_summary_intro' => 'We\'ve collected all forecasts from different providers. Here\'s the comparison:',
    'email_final_summary_comparison' => 'Provider Comparison',
    'email_final_summary_snapshots' => 'Forecast count',
    'email_final_summary_avg_temp' => 'Average temperature',
    'email_final_summary_temp_range' => 'Temperature range',
    'email_final_summary_first_vs_last' => 'Forecast change',
    'email_final_summary_first' => 'First',
    'email_final_summary_last' => 'Last',
    'email_final_summary_tip' => 'Different providers may have different forecasts. Check the details to see the full history of changes.',
    'email_final_summary_view_full' => 'View Full Details',
    'email_final_summary_footer' => 'You\'re receiving this email because your weather reading has completed.',
];
