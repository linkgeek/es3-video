<?php


namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;

use App\Crontab\TaskCache;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\Crontab\Crontab;

use EasySwoole\Component\Di;

class EasySwooleEvent implements Event
{
    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
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

        //PoolManager::getInstance()->register(MysqlPool::class);
        //PoolManager::getInstance()->register(RedisPool::class);
        Crontab::getInstance()->addTask(TaskCache::class);
        /**
         * ****************   缓存服务    ****************
         */
        Cache::getInstance()->setTempDir(EASYSWOOLE_TEMP_DIR)->attachToServer(ServerManager::getInstance()->getSwooleServer());
    }
}