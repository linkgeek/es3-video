<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019/8/28
 * Email: zhangatle@gmail.com
 */

namespace App\Lib\AliyunSdk;

require_once EASYSWOOLE_ROOT.'/App/Lib/AliyunSdk/aliyun-php-sdk-core/Config.php';
require_once EASYSWOOLE_ROOT.'/App/Lib/AliyunSdk/aliyun-php-sdk-oss/autoload.php';

use OSS\OssClient;
use vod\Request\V20170321\CreateUploadVideoRequest;
use vod\Request\V20170321\GetPlayInfoRequest;

class AliVod
{
    public $regionId = "cn-shanghai";
    public $client;
    public $ossClient;

    public function __construct()
    {
        $profile = \DefaultProfile::getProfile($this->regionId,\Yaconf::get("aliyun.accessKeyId"),\Yaconf::get("aliyun.accessKeySecret"));
        $this->client = new \DefaultAcsClient($profile);
    }

    /**
     * 获取视频上传地址和凭证
     * @param $title
     * @param $videoFileName
     * @param array $other
     * @return mixed|\SimpleXMLElement
     * @throws \ClientException
     * @throws \ServerException
     */
    public function createUploadVideo($title,$videoFileName,$other = []) {
        $request = new CreateUploadVideoRequest();
        $request->setTitle($title);
        $request->setFileName($videoFileName);
        if(!empty($other['description'])){
            $request->setDescription($other['description']);
        }
        $request->setCoverURL("http://img.alicdn.com/tps/TB1qnJ1PVXXXXXCXXXXXXXXXXXX-700-700.png");
        $request->setTags("tag1,tag2");
        $request->setAcceptFormat('JSON');

        $result = $this->client->getAcsResponse($request);
        if(empty($request) || empty($result->VideoId)){
            throw new \Exception('获取上传凭证不合法');
        }
        return $result;
    }

    /**
     * 初始化阿里云OSS
     * @param $uploadAuth
     * @param $uploadAddress
     * @throws \OSS\Core\OssException
     */
    public function initOssClient($uploadAuth,$uploadAddress){
        $this->ossClient = new OssClient($uploadAuth['AccessKeyId'],$uploadAuth['AccessKeySecret'],$uploadAddress['Endpoint'],false,$uploadAuth['SecurityToken']);
        $this->ossClient->setTimeout(86400*7);
        $this->ossClient->setConnectTimeout(10);
    }

    /**
     * 上传本地文件
     * @param $uploadAddress
     * @param $localFile
     * @return mixed
     */
    public function uploadLocalFile($uploadAddress,$localFile){
        return $this->ossClient->uploadFile($uploadAddress['Bucket'],$uploadAddress['FileName'],$localFile);
    }

    /**
     * 根据视频ID获取视频
     * @param int $videoId
     * @return array|mixed|\SimpleXMLElement
     * @throws \ClientException
     * @throws \ServerException
     */
    public function getPlayInfo($videoId = 0){
        if(empty($videoId)){
            return [];
        }
        $request = new GetPlayInfoRequest();
        $request->setVideoId($videoId);
        $request->setAcceptFormat("JSON");
        return $this->client->getAcsResponse($request);
    }
}
