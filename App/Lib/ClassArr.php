<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019/8/26
 * Email: zhangatle@gmail.com
 */
namespace App\Lib;

/**
 * 反射机制相关处理
 * Class ClassArr
 * @package App\Lib
 */
class ClassArr{
    public function uploadClassStat(){
        return [
            'image' => '\App\Lib\Upload\Image',
            'video' => '\App\Lib\Upload\Video',
        ];
    }

    public function initClass($type,$supportedClass,$params = [],$needInstance = true){
        if(!array_key_exists($type,$supportedClass)){
            return false;
        }
        $className = $supportedClass[$type];
        return $needInstance ? (new \ReflectionClass($className))->newInstanceArgs($params) : $className;
    }
}