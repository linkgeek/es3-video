<?php

namespace App\HttpController;

use App\Model\Es\EsVideo;
use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;
use App\Model\Pool\Mysql\Video as VideoPool;
use EasySwoole\Pool\Manager;

class Test extends Controller {

    public function testRedis() {
        // 取出连接池管理对象，然后获取连接对象（getObject）
        $redis1 = Manager::getInstance()->get('redis1')->getObj();

        $redis1->set('name', '仙士可');
        var_dump($redis1->get('name'));

        // 回收连接对象（将连接对象重新归还到连接池，方便后续使用）
        Manager::getInstance()->get('redis1')->recycleObj($redis1);
    }

    public function testDb() {
        //从连接池 中获得对象
        $client = Manager::getInstance()->get('db1')->getObj();
        //查询 video 表中所有的数据
        $client->queryBuilder()->get('video');
        $data = $client->execBuilder();

        //回收对象
        Manager::getInstance()->get('db1')->recycleObj($client);

        return $this->writeJson(201, $data,'ok');
    }

    public function index() {
        $params = [
            'index' => 'imooc_video',
            'id'    => '1',
            'body'  => ['testField' => 'abc']
        ];

        $client = ClientBuilder::create()->setHosts(["127.0.0.1:9094"])->build();
        //$response = $client->index($params);
        $response = $client->get($params);
        print_r($response);
    }

    public function test() {
        $params = [
            'index' => 'imooc_video',
            //'id'    => '1',
            'body'  => [
                'query' => [
                    'match' => [
                        'name' => 'test'
                    ]
                ]
            ]
        ];

        $client = Di::getInstance()->get('ES');
        //$response = $client->index($params);
        $response = $client->search($params);
        $this->writeJson(200, $response, 'ok');
    }

    public function demo() {
        $response = (new EsVideo())->searchByName('test', 1, 5);
        $this->writeJson(200, $response, 'ok');
    }

    public function yaconf() {
        $video = \Yaconf::get('redis');
        return $this->writeJson(201, 'ok', $video);
    }

    public function testGet() {
        $obj = new VideoPool();
        $result = $obj->getById(1);
        return $this->writeJson(201, 'ok', $result);
    }
}