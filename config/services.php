<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for packages to locate their credentials.
    |
    */
    'bioverify_pdf' => [
        'url' => env('BIOVERIFY_PDF_URL', 'http://127.0.0.1:8002'),
    ],

    'bioverify_chat' => [
        'url' => env('BIOVERIFY_CHAT_URL', 'http://127.0.0.1:8002'),
    ],

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

    /*
    |--------------------------------------------------------------------------
    | BioVerify AI Service
    |--------------------------------------------------------------------------
    */

    'bioverify_ai' => [
        'url' => env(
            'BIOVERIFY_AI_URL',
            'http://127.0.0.1:8001'
        ),

        // Must match MATCH_THRESHOLD in python-ai/main.py. Used to stop
        // the same face being enrolled under two different people.
        'face_match_threshold' => 0.50,

        // Must match VOICE_MODEL_VERSION / embedding size in python-ai/main.py.
        // Voice samples enrolled with another model are not compared.
        'voice_model_version' => 'wespeaker-resnet34-lm-v1',
        'voice_embedding_size' => 256,
    ],

];
