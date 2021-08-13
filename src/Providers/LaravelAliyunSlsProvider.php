<?php

namespace Islenbo\LaravelAliyunSls\Providers;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use Islenbo\LaravelAliyunSls\Formatters\AliyunSlsFormatter;
use Islenbo\LaravelAliyunSls\Handlers\AliyunSlsBufferHandler;
use Islenbo\LaravelAliyunSls\Handlers\AliyunSlsHandler;

class LaravelAliyunSlsProvider extends ServiceProvider
{
    /** @var Repository */
    private $config;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->config = $this->app->get('config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->config->set('logging.channels.aliyun-sls', $this->getChannel());
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }

    private function getChannel(): array
    {
        $slsConfig = $this->config->get('logging.aliyun-sls');
        return [
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
            'formatter' => class_exists($slsConfig['formatter']) ? $slsConfig['formatter'] : AliyunSlsFormatter::class,
        ];
    }
}
