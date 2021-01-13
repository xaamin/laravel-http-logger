<?php

use Xaamin\HttpLogger\Support\DefaultLogProfile;

return [
    /*
    |--------------------------------------------------------------------------
    | Default Logger Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default logger "driver" that will be used on
    | requests. By default, we will use the file driver but you may specify
    | any of the other drivers provided here.
    |
    | Supported: "file", "database"
    |
    */
    'driver' => env('HTTP_LOGGER_DRIVER', 'file'),

    'connection' => env('HTTP_LOGGER_DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Logger Database Table
    |--------------------------------------------------------------------------
    |
    | When using the "database" logger driver, you may specify the table we
    | should use to manage the logs. Of course, a sensible default is
    | provided for you; however, you are free to change this as needed.
    |
    */
    'table' => env('HTTP_LOGGER_TABLE', 'http_logs'),

    /*
    |--------------------------------------------------------------------------
    | Queue log writing
    |--------------------------------------------------------------------------
    |
    | This option allows you to control if the operations that logs writing
    | are queued. The value of this option will be used as the queue name, if
    | is set up to true then the default queue will be used.
    |
    | Available only for database driver.
    |
    */
    'queue' =>  env('HTTP_LOGGER_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Response logger
    |--------------------------------------------------------------------------
    |
    | This options allows you disable the response log if you set it up to false.
    |
    */
    'responses' =>  env('HTTP_LOGGER_LOG_RESPONSES', false),

    /*
    |--------------------------------------------------------------------------
    | HTTP verbs to be logged
    |--------------------------------------------------------------------------
    |
    | The http verbs allowed to log, it can be '*' to log all the requests.
    | Use this option only when no custom profiler is defined.
    |
    | Available: 'post', 'put', 'patch', 'delete', 'get', '*'
    |
    */
    'methods' => ['post', 'put', 'patch', 'delete', 'get'],

    /**
     * Custom meta generator to store into database. Works only for database
     * logging strategy.
     *
     * Implementation of Xaamin\HttpLogger\Contracts\LogInputGeneratorInterface
     */
    'generator' => null,

    /*
     * The log profile which determines whether a request should be logged.
     * It should implement `LogProfile`.
     */
    'log_profile' => DefaultLogProfile::class,

    /*
    |--------------------------------------------------------------------------
    | Field filtering
    |--------------------------------------------------------------------------
    |
    | Filter out body fields which will never be logged.
    |
    */
    'except' => [
        'password',
        'password_confirmation'
    ],

    'headers' => [
        'only' => null,
        'except' => [
            'Authorization'
        ]
    ]
];
