<?php

namespace App\HttpController\Api;

use App\Lib\Pool\RedisPool;
use App\Model\Es\EsVideo;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Http\Message\Status;
use EasySwoole\Pool\Manager;
use EasySwoole\Validate\Validate;
use App\Model\Video as VideoModel;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

class Video extends Base {

    public $logType = "video:";

    /**
     * 保存视频配置
     */
    public function add() {
        $params = $this->request()->getRequestParam();
        Logger::getInstance()->log($this->logType . "add:" . json_encode($params));

        // 数据检验
        $validator = new Validate();
        $validator->addColumn('name', '视频名称')->required()->lengthMin(2)->lengthMax(20);
        $validator->addColumn('url', '视频地址')->required();
        $validator->addColumn('image', '图片地址')->required();
        $validator->addColumn('content', '视频描述')->required();
        $validator->addColumn('cat_id', '栏目ID')->required();

        if (!$validator->validate($params)) {
            return $this->writeJson(400, $validator->getError()->__toString());
        }

        $data = [
            'name'        => $params['name'],
            'cat_id'      => intval($params['cat_id']),
            'image'       => $params['image'],
            'url'         => $params['url'],
            'type'        => 1,
            'content'     => $params['content'],
            'uploader'    => 'test',
            'create_time' => time(),
            'update_time' => time(),
            'status'      => \Yaconf::get('status.normal'),
        ];
        //print_r($data);

        try {
            $modelObj = new VideoModel();
            $flag = $modelObj->add($data);
        } catch (\Exception $e) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, $e->getMessage());
        }

        if (!$flag) {
            return $this->writeJson(400, '提交视频有误');
        }

        $redis1 = Manager::getInstance()->get('redis1')->getObj();
        $id = $redis1->incr('VIDEO_AUTO_INCREMENT');
        // 回收连接对象（将连接对象重新归还到连接池，方便后续使用）
        Manager::getInstance()->get('redis1')->recycleObj($redis1);

        // 同步存在ES
        TaskManager::getInstance()->sync(function () use($id, $data) {
            $response = (new EsVideo())->insertByName($id, $data);
        });

        return $this->writeJson(200, 'OK');
    }

    public function index() {
        $id = intval($this->params['id']);
        if (empty($id)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, '请求不合法');
        }

        // 获取视频的基本信息
        try {
            $video = (new VideoModel())->getById($id);
        } catch (\Exception $e) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, '请求不合法');
        }

        if (!$video || $video['status'] != \Yaconf::get("status.normal")) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, '该视频不存在');
        }
        $video['video_duration'] = gmstrftime("%H:%M:%S", $video['video_duration']);

        // 异步播放数统计
        TaskManager::async(function () use ($id) {
            $redis = RedisPool::defer();
            // 总记数
            $redis->zincrby(\Yaconf::get("redis.video_play_key"), 1, $id);
            // 按天记数
            $redis->zincrby(\Yaconf::get("redis.video_play_key") . '_' . date('Ymd'), 1, $id);
        });
        return $this->writeJson(Status::CODE_OK, 'OK', $video);
    }

    /**
     * 视频点播总排行榜
     */
    public function rank() {
        $result = RedisPool::defer()->zrevrange(\Yaconf::get("redis.video_play_key"), 0, -1, "withscores");
        return $this->writeJson(Status::CODE_OK, 'OK', $result);
    }

    /**
     * 视频点赞逻辑
     */
    public function love() {
        $videoId = intval($this->params['videoId']);
        if (empty($videoId)) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, '参数不合法');
        }
        // 判断videoId是否存在 TODO
        $redis = RedisPool::defer();
        $redis->zincrby(\Yaconf::get("redis.video_love"), 1, $videoId);
        // 视频-点赞-用户 绑定记录 TODO
    }
}
