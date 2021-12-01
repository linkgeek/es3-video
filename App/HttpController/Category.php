<?php

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;

class Category extends Controller {
    public function index() {
        $video = [
            'id' => 1,
            'name'=> 'test'
        ];
        return $this->writeJson(200, 'ok', $video);
    }
}