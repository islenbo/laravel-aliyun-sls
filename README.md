# laravel-aliyun-sls

## Using
```shell
composer require islenbo/laravel-aliyun-sls
```

add service provider to app.php
```PHP
<?php
use \Islenbo\LaravelAliyunSls\Providers\LaravelAliyunSlsProvider;

return [
    // ...
    'providers' => [
        /*
         * Package Service Providers...
         */
        LaravelAliyunSlsProvider::class,
    ],
    // ...
];
```

add config to logging.php
```PHP
<?php
return [
    // ...

    // Aliyun SLS config
    'aliyun-sls' => [
        'endpoint' => env('ALIYUN_LOG_ENDPOINT'),
        'accessKeyId' => env('ALIYUN_LOG_ACCESSKEYID'),
        'accessKey' => env('ALIYUN_LOG_ACCESSKEY'),
        'project' => env('ALIYUN_LOG_PROJECT'),
        'logstore' => env('ALIYUN_LOG_LOGSTORE'),
        'bufferLimit' => env('ALIYUN_LOG_BUFFER_LIMIT', 5),
    ],

    // ...
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            // assign aliyun-sls
            'channels' => ['aliyun-sls'],
            'ignore_exceptions' => false,
            'tap' => [
            ]
        ],
        // ...
    ],
];
```
