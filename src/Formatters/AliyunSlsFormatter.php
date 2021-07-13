<?php

namespace Islenbo\LaravelAliyunSls\Formatters;

use Aliyun_Log_Models_LogItem;
use Illuminate\Support\Arr;
use Monolog\DateTimeImmutable;
use Monolog\Formatter\FormatterInterface;

class AliyunSlsFormatter implements FormatterInterface
{

    public function format(array $record)
    {
        /** @var DateTimeImmutable $datetime */
        $datetime = $record['datetime'];
        $uid = Arr::pull($record, 'extra.uid', '');

        $logItem = new Aliyun_Log_Models_LogItem();
        $logItem->setTime($datetime->getTimestamp());
        $logItem->setContents([
            'message' => $record['message'],
            'level' => $record['level_name'],
            'env' => $record['channel'],
            'uid' => $uid,
            'context' => $this->convert($record['context']),
            'extra' => $this->convert($record['extra']),
        ]);

        return $logItem;
    }

    public function formatBatch(array $records)
    {
        $result = [];
        foreach ($records as $record) {
            $result[] = $this->format($record);
        }
        return $result;
    }

    private function convert(array $data): string
    {
        $newData = [];
        foreach ($data as $k => $v) {
            $newData[] = $k . ':' . json_encode($v, JSON_UNESCAPED_UNICODE);
        }

        return implode(PHP_EOL, $newData);
    }
}
