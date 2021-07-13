<?php

namespace Islenbo\LaravelAliyunSls\Handlers;

use Aliyun_Log_Client;
use Aliyun_Log_Models_PutLogsRequest;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\FormattableHandlerTrait;
use Monolog\Handler\ProcessableHandlerTrait;
use Monolog\Logger;

class AliyunSlsHandler extends AbstractHandler implements FormattableHandlerInterface
{
    use ProcessableHandlerTrait, FormattableHandlerTrait;

    /** @var string */
    private $project;

    /** @var string */
    private $logstore;

    /** @var Aliyun_Log_Client */
    private $slsClient;

    private $topic = '';

    public function __construct(string $endpoint, string $accessKeyId, string $accessKey, string $project, string $logstore, int $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->project = $project;
        $this->logstore = $logstore;
        $this->slsClient = new Aliyun_Log_Client($endpoint, $accessKeyId, $accessKey);
    }

    public function handleBatch(array $records): void
    {
        $logItems = [];
        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }

            if ($this->processors) {
                $record = $this->processRecord($record);
            }

            $logItems[] = $this->getFormatter()->format($record);
        }
        $this->putLogs($logItems);
    }

    public function handle(array $record): bool
    {
        $this->handleBatch([$record]);
        return false === $this->bubble;
    }

    private function putLogs(array $logItems): void
    {
        $this->slsClient->putLogs(
            new Aliyun_Log_Models_PutLogsRequest($this->project, $this->logstore, $this->topic, gethostname(), $logItems)
        );
    }

}
