<?php

namespace App\Lib\Pool;

use EasySwoole\Pool\AbstractPool;
use EasySwoole\Pool\Config;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\Redis\Redis;


class RedisPool extends AbstractPool {
    protected $redisConfig;

    public function __construct(Config $conf, RedisConfig $redisConfig) {
        parent::__construct($conf);
        $this->redisConfig = $redisConfig;
    }

    protected function createObject() {
        // 根据传入的 redis 配置进行 new 一个 redis 连接
        $redis = new Redis($this->redisConfig);
        return $redis;
    }
}