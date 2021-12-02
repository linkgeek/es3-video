<?php

/**
 * 消息队列 消费者
 * php consumer
 */

namespace App\Lib\Process;

use EasySwoole\Component\Di;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\Logger;
use Swoole\Process;

class ConsumerTest extends AbstractProcess {
    private $isRun = false;

    public function run($arg) {
        // TODO: Implement run() method.
        /*
         * 举例，消费redis中的队列数据
         * 定时500ms检测有没有任务，有的话就while死循环执行
         */
        $this->addTick(500, function () {
            if (!$this->isRun) {
                $this->isRun = true;

                while (true) {
                    try {
                        //获取队列 lpop
                        $task = Di::getInstance()->get("REDIS")->lPop('geng_list_test');
                        if ($task) {
                            var_dump($this->getProcessName() . "---" . $task);
                            //处理lpop 邮件 推送 短信等
                            //写Log
                            $log = new Logger();
                            $log->log($this->getProcessName() . "---" . $task);
                        } else {
                            break;
                        }
                    } catch (\Throwable $throwable) {
                        break;
                    }
                }
                $this->isRun = false;
            }
//            var_dump($this->getProcessName().' task run check');
        });
    }

    public function onShutDown() {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str, ...$args) {
        // TODO: Implement onReceive() method.
    }
}