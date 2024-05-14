<?php

use Illuminate\Support\Facades\Cache;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        // 心跳
        'heartbeat' => [
            'driver' => 'daily',
            'path' => storage_path('logs/heartbeat/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => null,
        ],
        // 漏电
        'alarm' => [
            'driver' => 'daily',
            'path' => storage_path('logs/alarm/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => null,
        ],

        'heartbeat_log' => [
            'driver' => 'single',
            'path' => storage_path('logs/heartbeat/my_custom_log.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'hikvision' => [
            'driver' => 'daily',
            'path' => storage_path('logs/hikvision/hikvision.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 60,
        ],
        'liurui' => [
            'driver' => 'daily',
            'path' => storage_path('logs/liurui/liurui.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 60,
        ],
        'liurui_ontnet' => [
            'driver' => 'daily',
            'path' => storage_path('logs/liurui/liurui_ontnet.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 60,
        ],

        'haiman' => [
            'driver' => 'daily',
            'path' => storage_path('logs/haiman/haiman.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 60,
        ],

        // 'my_custom_log' => [
        //     'driver' => 'single',
        //     'path' => tap(storage_path('logs/my_custom_log.log'), function ($path) {
        //         // 从缓存获取动态文件名，如果存在则使用缓存的文件名
        //         $dynamicLogName = cache::get('key',time());
        //         if ($dynamicLogName) {
        //             $path = storage_path("logs/{$dynamicLogName}.log");
        //         }
        //
        //         return $path;
        //     }),
        //     'level' => 'debug',
        // ],

        'alarm_log' => [
            'driver' => 'single',
            'path' => 'logs/alarm/my_custom_log.log',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 60,
        ],

        'dynamiclog' => [
            'driver' => 'custom',
            'via' => \App\Logging\CreateDynamicLogger::class,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];
