<?php

namespace App\Crontab;

use App\Model\Video;
use EasySwoole\EasySwoole\Crontab\AbstractCronTask;

class TaskCache extends AbstractCronTask
{
    public static function getRule(): string
    {
        return '* * * * *'; // 每分钟执行一次
    }

    public static function getTaskName(): string
    {
        return 'taskCache';
    }

    /**
     * 设置首页缓存
     * @param \swoole_server $server
     * @param int $taskId
     * @param int $fromWorkerId
     * @param null $flags
     * @throws \Exception
     */
    static function run(\swoole_server $server, int $taskId, int $fromWorkerId, $flags = null)
    {
        $videoModel = new Video();
        $videoModel->setIndexVideo();
    }

    function onException(\Throwable $throwable, int $taskId, int $workerIndex) {
        // TODO: Implement onException() method.
    }
}
