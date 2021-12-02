<?php


namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

//use App\Crontab\TaskCache;
use App\Lib\Pool\MysqlPool;
use App\Lib\Pool\RedisPool;
use EasySwoole\Pool\Manager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Crontab\Crontab;

use EasySwoole\Component\Di;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        //$dbConf = \Yaconf::get('mysql');
        Manager::getInstance()->register(MysqlPool::class);
    }

    public static function mainServerCreate(EventRegister $register)
    {
        //mysql相关
        //Di 依赖注入
        Di::getInstance()->set('MYSQL', \MysqliDb::class, Array(
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '000000',
            'db' => 'blog',
            'port' => 3306,
            'charset' => 'utf8',
        ));

        Di::getInstance()->set('REDIS', \App\Lib\Redis\Redis::getInstance());
        Di::getInstance()->set('ES', \App\Model\Es\EsClient::getInstance());

        //Manager::getInstance()->register(MysqlPool::class);
        //PoolManager::getInstance()->register(RedisPool::class);
        //Crontab::getInstance()->addTask(TaskCache::class);
        /**
         * ****************   缓存服务    ****************
         */
        //Cache::getInstance()->setTempDir(EASYSWOOLE_TEMP_DIR)->attachToServer(ServerManager::getInstance()->getSwooleServer());
    }
}