<?php

return [
	/*
	* Add the Firebase API key
    */
    'fcm' => [
        'api_key' => env('FCM_API_KEY', '')
    ],
    'apn' => [
        'dev' => json_decode(env('APN_KEY_DEV'), true),
        'pro' => json_decode(env('APN_KEY_PRO'), true)
    ],
    'esms' => [
        'esms_api_key'    => env('ESMS_API_KEY'),
        'esms_secret_key' => env('ESMS_SECRET_KEY'),
        'esms_sms_type'   => env('ESMS_SMS_TYPE'),
        'esms_url'        => env('ESMS_URL'),
        'esms_brand_name' => env('ESMS_BRAND_NAME'),
        'esms_max_send' => env('ESMS_DAY_MAX'),
    ]
];