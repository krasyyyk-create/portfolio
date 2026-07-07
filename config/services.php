<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'api_sports' => [
        'key' => env('API_SPORTS_KEY'),
        'base_url' => env('API_SPORTS_BASE_URL', 'https://v1.formula-1.api-sports.io'),
    ],

    'contact' => [
        'email' => env('CONTACT_EMAIL', 'krasyyy.k@gmail.com'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'google_calendar' => [
        'calendar_id' => env('GOOGLE_CALENDAR_ID', 'primary'),
        'refresh_token' => env('GOOGLE_CALENDAR_REFRESH_TOKEN'),
        'timezone' => env('GOOGLE_CALENDAR_TIMEZONE', env('APP_TIMEZONE', 'UTC')),
        'working_days' => [1, 2, 3, 4, 5],
        'start_hour' => (int) env('GOOGLE_CALENDAR_START_HOUR', 9),
        'end_hour' => (int) env('GOOGLE_CALENDAR_END_HOUR', 17),
        'slot_duration_minutes' => (int) env('GOOGLE_CALENDAR_SLOT_MINUTES', 60),
        'advance_days' => (int) env('GOOGLE_CALENDAR_ADVANCE_DAYS', 30),
    ],

];
