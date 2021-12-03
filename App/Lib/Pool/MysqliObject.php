<?php

namespace App\Lib\Pool;

use EasySwoole\Pool\Config;
use EasySwoole\Pool\Manager;

class MysqliObject extends \EasySwoole\Mysqli\Client implements \EasySwoole\Pool\ObjectInterface {

    public function __construct() {
        $mysqlConfig = \Yaconf::get('mysql');
        //$mysqlConfig = Config::getInstance()->getConf('mysql');
        parent::__construct(new \EasySwoole\Mysqli\Config($mysqlConfig));
    }


    // 被连接池 回收的时候执行
    public function objectRestore() {

    }

    // 取出连接池的时候被调用，若返回false，则当前对象被弃用回收
    public function beforeUse(): ?bool {
        return true;
    }

    // 被连接池 unset 的时候执行
    public function gc() {
        $this->close();
    }
}