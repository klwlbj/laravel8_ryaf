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

    'mailgun'          => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark'         => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses'              => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    // 电信AEP平台
    'ctwing'           => [
        'key'    => env('CTWING_KEY'),
        'secret' => env('CTWING_SECRET'),
    ],
    // NB手报
    'nb_manual_alarm'  => [
        'key' => env('NB_KEY'),
    ],
    // 消控主机
    'fire_alarm_panel' => [
        'push_api' => env('PUSH_API'),
    ],
    // 移动平台
    'onenet'           => [
        'user_id'    => env("ONE_NET_USERID"),
        'access_key' => env('ONE_NET_ACCESS_KEY'),
    ],
    // 海康
    'hikvision'        => [
        'app_key'    => env('HIK_KEY', ''),
        'app_secret' => env('HIK_SECRET', ''),
    ],
    // 海幢街道
    'haizhuang'        => [
        'account'  => env('HAIZHUANG_ACCOUNT', ),
        'password' => env('HAIZHUANG_APPSECRET'),
    ],
];
