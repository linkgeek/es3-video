<?php

namespace App\HttpController;

use App\Model\Es\EsVideo;
use EasySwoole\Component\Di;
use EasySwoole\Http\AbstractInterface\Controller;
use App\Model\Pool\Mysql\Video as VideoPool;

class Test extends Controller {

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

    public function testDb() {
        $obj = new VideoPool();
        $result = $obj->getById(1);
        return $this->writeJson(201, 'ok', $result);
    }
}