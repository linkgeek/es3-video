<?php
namespace App\HttpController;

use App\Lib\AliyunSdk\AliVod;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;

class Index extends Base
{
    function index()
    {
        var_dump(\Yaconf::get("aliyun.accessKeyId"));
    }

    /**
     * 测试阿里云视频点播上传
     * @throws \ClientException
     * @throws \OSS\Core\OssException
     * @throws \ServerException
     */
    function testali(){
        $obj = new AliVod();
        $title = "test";
        $videoName = "1.mp4";
        $result = $obj->createUploadVideo($title,$videoName);
        $uploadAddress = json_decode(base64_decode($result->UploadAddress),true);
        $uploadAuth = json_decode(base64_decode($result->UploadAuth),true);
        $obj->initOssClient($uploadAuth,$uploadAddress);
        $videoFile = "/www/esapi/movie.mp4";
        $result = $obj->uploadLocalFile($uploadAddress,$videoFile);
        print_r($result);
    }

    /**
     * 测试获取视频
     * @throws \ClientException
     * @throws \ServerException
     */
    public function getVideo(){
        $videoId = "e39634ed48b644338fd8dd19c4c2f5d5";
        $obj = new AliVod();
        print_r($obj->getPlayInfo($videoId));
    }
}
