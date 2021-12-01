<?php

namespace App\Lib\Redis;

use Composer\Config;
use EasySwoole\Component\Singleton;

class Redis {
    use Singleton;

    public $redis = "";

    private function __construct() {
        if (!extension_loaded('redis')) {
            throw new \Exception("redis.so不存在");
        }

        try {
            $redisConfig = \Yaconf::get('redis');
            $this->redis = new \Redis();
            $res = $this->redis->connect($redisConfig['host'], $redisConfig['port'], $redisConfig['time_out']);
            $this->redis->auth($redisConfig['auth']);
        } catch (\Exception $e) {
            throw new \Exception("redis服务异常");
        }

        if ($res === false) {
            throw new \Exception("redis连接失败");
        }
    }

    public function set($key, $value, $time = 0) {
        if (empty($key)) {
            return '';
        }
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (!$time) {
            return $this->redis->set($key, $value);
        }
        return $this->redis->setex($key, $time, $value);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function get($key) {
        if (empty($key)) {
            return '';
        }

        return $this->redis->get($key);
    }

    /**
     * @param $key
     * @return string
     */
    public function lPop($key) {
        if (empty($key)) {
            return '';
        }

        return $this->redis->lPop($key);
    }

    /**
     * 消息生产者 进入消息队列
     * @param $key
     * @param $value
     * @return bool|int|string
     */
    public function rPush($key, $value) {
        if (empty($key)) {
            return '';
        }

        return $this->redis->rPush($key, $value);
    }

    /**
     * @param $key
     * @param $number
     * @param $member
     * @return bool|float
     */
    public function zincrby($key, $number, $member) {
        if (empty($key) || empty($member)) {
            return false;
        }

        return $this->redis->zincrby($key, $number, $member);
    }

    /**
     * @param $key
     * @param $start
     * @param $stop
     * @param $type
     * @return array|bool
     */
    /*public function zrevrange($key, $start, $stop, $type) {
        if(empty($key)) {
            return false;
        }

        return $this->redis->zrevrange($key, $start, $stop, $type);
    }*/

    /**
     * 当类中不存在该方法时候，直接调用call 实现调用底层redis相关的方法
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        return $this->redis->$name(...$arguments);
    }
}