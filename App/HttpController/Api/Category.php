<?php

namespace App\HttpController\Api;

class Category extends Base {
    public function index() {
        $config = \Yaconf::get('category.cats');
        return $this->writeJson(200, 'OK', $config);
    }

}