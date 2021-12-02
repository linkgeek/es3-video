<?php

namespace App\Lib\Pool;

use EasySwoole\Pool\AbstractPool;

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

    /*protected function createObject() {
        // TODO: Implement createObject() method.
        $redis = new RedisObject();
        ///$conf = Config::getInstance()->getConf('REDIS');
        $conf = \Yaconf::get("redis");
        if ($redis->connect($conf['host'], $conf['port'])) {
            if (!empty($conf['auth'])) {
                $redis->auth($conf['auth']);
            }
            return $redis;
        } else {
            return null;
        }
    }*/
}