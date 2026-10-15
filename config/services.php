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

        // Face liveness for the public verification kiosk. A deterrent
        // against printed photos and phone screens, not a certified
        // presentation-attack detection system. Administrators testing
        // in the recognition console are exempt.
        'face_liveness' => [
            'required' => env('FACE_LIVENESS_REQUIRED', true),
            // Passive model score needed for a frame to count as live.
            'threshold' => (float) env('FACE_LIVENESS_THRESHOLD', 0.80),
            // Good-quality frames that must score live during one attempt.
            'min_live_frames' => 2,
            // Head-turn challenge: face forward, turn to either side, return.
            'yaw_frontal' => 0.12,
            'yaw_turned' => 0.30,
            // Seconds an attempt may take, and how long a pass stays valid.
            'attempt_seconds' => 90,
            'valid_seconds' => 60,
        ],

        // Capture guidance limits (frame-relative face size, 0-255 brightness).
        'face_capture' => [
            'min_face_width' => 0.18,
            'max_face_width' => 0.60,
            'max_center_offset' => 0.14,
            'min_brightness' => 90,
            'max_brightness' => 225,
            'min_sharpness' => 60,
        ],

        // Must match VOICE_MODEL_VERSION / embedding size in python-ai/main.py.
        // Voice samples enrolled with another model are not compared.
        'voice_model_version' => 'wespeaker-resnet34-lm-v1',
        'voice_embedding_size' => 256,
    ],

];
