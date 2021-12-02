<?php

namespace App\Model\Es;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Logger;
use Elasticsearch\ClientBuilder;

class EsClient {
    use Singleton;

    private $client = null;

    private function __construct() {
        $conf = \Yaconf::get('es');
        try {
            $this->client = ClientBuilder::create()->setHosts([$conf['host'] . ':' . $conf['port']])->build();
        } catch (\Exception $e) {
            Logger::getInstance()->log('es连接失败：' . $e->getMessage());
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        // TODO: Implement __call() method.
        return $this->client->$name(...$arguments);
    }
}