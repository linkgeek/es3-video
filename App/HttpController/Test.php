<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

class Test extends Controller {
    public function index() {
        $video = [
            'id' => 222,
            'name'=> 'test'
        ];
        return $this->writeJson(201, 'ok', $video);
    }

    public function yaconf() {
        $video = \Yaconf::get('redis');
        return $this->writeJson(201, 'ok', $video);
    }
}