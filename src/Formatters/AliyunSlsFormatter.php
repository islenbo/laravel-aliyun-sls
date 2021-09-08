<?php

namespace Islenbo\LaravelAliyunSls\Formatters;

use Aliyun_Log_Models_LogItem;
use Illuminate\Support\Arr;
use Monolog\DateTimeImmutable;
use Monolog\Formatter\FormatterInterface;
use Throwable;

class AliyunSlsFormatter implements FormatterInterface
{

    public function format(array $record)
    {
        /** @var DateTimeImmutable $datetime */
        $datetime = $record['datetime'];
        $uid = Arr::pull($record, 'extra.uid', '');

        $result = new Aliyun_Log_Models_LogItem();
        $result->setTime($datetime->getTimestamp());
        $result->setContents([
            'message' => $record['message'],
            'level' => $record['level_name'],
            'env' => $record['channel'],
            'uid' => $uid,
            'context' => $this->convert($record['context']),
            'extra' => $this->convert($record['extra']),
        ]);

        return $result;
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
        $result = [];
        foreach ($data as $k => $v) {
            if ($v instanceof Throwable) {
                $result[] = $k . ':'. $this->formatException($v);
            } else {
                $result[] = $k . ':' . json_encode($v, JSON_UNESCAPED_UNICODE);
            }
        }

        return implode(PHP_EOL, $result);
    }

    public function formatException(Throwable $e): string
    {
        $str = "[{$e->getCode()}] {$e->getMessage()}\n{$e->getFile()}:{$e->getLine()}\n{$e->getTraceAsString()}\n";
        if ($previous = $e->getPrevious()) {
            do {
                $str .= "[previous exception][{$previous->getCode()}] {$previous->getMessage()}\n{$previous->getFile()}:{$previous->getLine()}\n{$previous->getTraceAsString()}\n";
            } while ($previous = $previous->getPrevious());
        }
        return $str;
    }
}
