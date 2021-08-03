<?php

namespace Islenbo\LaravelAliyunSls\Providers;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Islenbo\LaravelAliyunSls\Formatters\AliyunSlsFormatter;
use Islenbo\LaravelAliyunSls\Handlers\AliyunSlsBufferHandler;
use Islenbo\LaravelAliyunSls\Handlers\AliyunSlsHandler;

class LaravelAliyunSlsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /** @var Repository $config */
        $config = $this->app->get('config');
        $slsConfig = $config->get('logging.aliyun-sls');
        $config->set('logging.channels.aliyun-sls', [
            'driver' => 'monolog',
            'handler' => AliyunSlsBufferHandler::class,
            'handler_with' => [
                'handlerConfig' => [
                    'handler' => AliyunSlsHandler::class,
                    'handler_with' => [
                        'endpoint' => $slsConfig['endpoint'],
                        'accessKeyId' => $slsConfig['accessKeyId'],
                        'accessKey' => $slsConfig['accessKey'],
                        'project' => $slsConfig['project'],
                        'logstore' => $slsConfig['logstore'],
                    ],
                ],
                'bufferLimit' => $slsConfig['bufferLimit'],
            ],
            'formatter' => AliyunSlsFormatter::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
