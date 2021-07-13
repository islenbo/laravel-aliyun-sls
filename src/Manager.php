<?php

namespace Islenbo\LaravelAliyunSls;

use Illuminate\Log\LogManager;
use Monolog\Handler\HandlerInterface;

class Manager extends LogManager
{

    public function makeHandler(array $config): HandlerInterface
    {
        return $this->prepareHandler(
            $this->app->make($config['handler'], $config['handler_with']), $config
        );
    }

}
