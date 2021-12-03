<?php

namespace App\Lib\Pool;

use EasySwoole\Pool\AbstractPool;
use EasySwoole\Pool\Config;

class MysqliPool extends AbstractPool {
    public function __construct(Config $conf) {
        parent::__construct($conf);
    }

    /**
     * 对象池创建对象时调用
     * @return MysqliObject
     */
    protected function createObject() {
        return new MysqliObject();
    }

}