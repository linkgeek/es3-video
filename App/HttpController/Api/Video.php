<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019/8/26
 * Email: zhangatle@gmail.com
 */

namespace App\HttpController\Api;

use App\Utility\Pool\RedisPool;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\Message\Status;
use EasySwoole\Validate\Validate;
use App\Model\Video as VideoModel;

class Video extends Base
{
    public $logType = "video:";
    /**
     * 添加视频
     */
    public function add(){
        $params = $this->request()->getRequestParam();
        Logger::getInstance()->log($this->logType."add:".json_encode($params));

        $validator = new Validate();
        $validator->addColumn('name','视频名称')->required()->lengthMin(2)->lengthMax(20);
        $validator->addColumn('url','视频地址')->required();
        $validator->addColumn('image','图片地址')->required();
        $validator->addColumn('content','视频描述')->required();
        $validator->addColumn('cat_id','栏目ID')->required();

        if(!$validator->validate($params)){
            return $this->writeJson(400,$validator->getError()->__toString(),[]);
        }

        $data = [
            'name' => $params['name'],
            'url' => $params['url'],
            'image' => $params['image'],
            'content' => $params['content'],
            'cat_id' => intval($params['cat_id']),
            'create_time' => time(),
            'uploader' => 'singwa',
            'status' => 1, // 0  1 2
        ];

        try{
            $modelObj = new VideoModel();
            $videoId = $modelObj->add($data);
        }catch (\Exception $e){
            return $this->writeJson(400,$e->getMessage());
        }

        if(!empty($videoId)){
            return $this->writeJson(200,'OK',['id'=>$videoId]);
        }else{
            return $this->writeJson(400,'提交视频有误',['id'=>0]);
        }
    }

    public function index()
    {
        $id = intval($this->params['id']);
        var_dump($id);
        if(empty($id)){
            return $this->writeJson(Status::CODE_BAD_REQUEST,'请求不合法');
        }
        // 获取视频的基本信息
        try{
            $video = (new VideoModel())->getById($id);
        }catch (\Exception $e){
            return $this->writeJson(Status::CODE_BAD_REQUEST,'请求不合法');
        }
        var_dump($video);
        if(!$video || $video['status'] != \Yaconf::get("status.normal")){
            return $this->writeJson(Status::CODE_BAD_REQUEST,'该视频不存在');
        }
        $video['video_duration'] = gmstrftime("%H:%M:%S",$video['video_duration']);
        // 异步播放数统计
        TaskManager::async(function () use ($id) {
            $redis = RedisPool::defer();
            $res = $redis->zincrby(\Yaconf::get("redis.video_play_key"),1,$id); // 增加播放次数统计
        });
        return $this->writeJson(Status::CODE_OK,'OK',$video);
    }


    /**
     * 排行榜
     */
    public function rank(){
        $result = RedisPool::defer()->zrevrange(\Yaconf::get("redis.video_play_key"),0,-1,"withscores");
        return $this->writeJson(Status::CODE_OK,'OK',$result);
    }

    /**
     * 点选逻辑
     */
    public function love(){
        $videoId = intval($this->params['videoId']);
        if(empty($videoId)){
            return $this->writeJson(Status::CODE_BAD_REQUEST,'参数不合法');
        }
        $redis = RedisPool::defer();
        $res = $redis->zincrby(\Yaconf::get("redis.video_love"),1,$videoId);
    }
}
