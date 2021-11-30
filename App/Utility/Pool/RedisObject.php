<?php


namespace App\Utility\Pool;


use Co\Redis;
use EasySwoole\Component\Pool\PoolObjectInterface;

class RedisObject extends Redis implements PoolObjectInterface
{
    function gc()
    {
        $this->close();
    }

    function objectRestore()
    {
        // TODO: Implement objectRestore() method.
    }

    function beforeUse(): bool
    {
        return true;
    }
}