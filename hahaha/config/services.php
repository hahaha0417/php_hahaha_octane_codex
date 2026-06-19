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

    'trello' => [
        'base_url' => env('TRELLO_BASE_URL', 'https://api.trello.com/1'),
        'key' => env('TRELLO_KEY'),
        'token' => env('TRELLO_TOKEN'),
        'timeout' => env('TRELLO_TIMEOUT', 10),
        'connect_timeout' => env('TRELLO_CONNECT_TIMEOUT', 3),
    ],

    'jira' => [
        'base_url' => env('JIRA_BASE_URL'),
        'agile_base_url' => env('JIRA_AGILE_BASE_URL'),
        'email' => env('JIRA_EMAIL'),
        'api_token' => env('JIRA_API_TOKEN'),
        'timeout' => env('JIRA_TIMEOUT', 10),
        'connect_timeout' => env('JIRA_CONNECT_TIMEOUT', 3),
    ],

    'github' => [
        'base_url' => env('GITHUB_BASE_URL', 'https://api.github.com'),
        'token' => env('GITHUB_TOKEN'),
        'api_version' => env('GITHUB_API_VERSION', '2022-11-28'),
        'timeout' => env('GITHUB_TIMEOUT', 10),
        'connect_timeout' => env('GITHUB_CONNECT_TIMEOUT', 3),
    ],

    'outlook' => [
        'base_url' => env('OUTLOOK_BASE_URL', 'https://graph.microsoft.com/v1.0'),
        'token' => env('OUTLOOK_TOKEN'),
        'timeout' => env('OUTLOOK_TIMEOUT', 10),
        'connect_timeout' => env('OUTLOOK_CONNECT_TIMEOUT', 3),
    ],

    'gmail' => [
        'base_url' => env('GMAIL_BASE_URL', 'https://gmail.googleapis.com/gmail/v1'),
        'token' => env('GMAIL_TOKEN'),
        'timeout' => env('GMAIL_TIMEOUT', 10),
        'connect_timeout' => env('GMAIL_CONNECT_TIMEOUT', 3),
    ],

    'line' => [
        'base_url' => env('LINE_BASE_URL', 'https://api.line.me/v2/bot'),
        'channel_access_token' => env('LINE_CHANNEL_ACCESS_TOKEN'),
        'timeout' => env('LINE_TIMEOUT', 10),
        'connect_timeout' => env('LINE_CONNECT_TIMEOUT', 3),
    ],

];
