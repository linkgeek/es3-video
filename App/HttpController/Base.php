<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;

class Base extends Controller
{
    public function index()
    {

    }

    public function onRequest(?string $action): ?bool
    {
        return parent::onRequest($action); // TODO: Change the autogenerated stub
    }

//    public function onException(\Throwable $throwable): void
//    {
//        $this->writeJson(400,'服务器内部错误',[]);
//    }
}
