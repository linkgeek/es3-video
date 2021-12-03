<?php


namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

use App\Lib\Pool\MysqliPool;
use App\Lib\Pool\RedisPool;
use EasySwoole\Pool\Manager;
use EasySwoole\Pool\Config;
use EasySwoole\Component\Di;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');

        $config = new Config();

        // 注册mysqli 连接池
        Manager::getInstance()->register(new MysqliPool($config), 'db1');

        $redisConf1 = \Yaconf::get('redis');
        $redisConfig1 = new \EasySwoole\Redis\Config\RedisConfig($redisConf1);
        // 注册连接池管理对象
        Manager::getInstance()->register(new RedisPool($config, $redisConfig1), 'redis1');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        //Di 依赖注入
        //Di::getInstance()->set('REDIS', \App\Lib\Redis\Redis::getInstance());
        Di::getInstance()->set('ES', \App\Model\Es\EsClient::getInstance());
    }
}