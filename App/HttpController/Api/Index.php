<?php

namespace App\HttpController\Api;

use EasySwoole\Http\Message\Status;
use App\Model\Video as VideoModel;

class Index extends Base {
    /**
     * 原始方案，根据mysql读取数据
     */
    public function listsSql() {
        $condition = [];
        if (!empty($this->params['cat_id'])) {
            $condition['cat_id'] = intval($this->params['cat_id']);
        }
        try {
            $videoModel = new VideoModel();
            $data = $videoModel->getVideoData($condition, $this->params['page'], $this->params['size']);
        } catch (\Exception $e) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, '服务异常');
        }
        if (!empty($data['lists'])) {
            foreach ($data['lists'] as &$list) {
                $list['create_time'] = date("Ymd H:i:s", $list['create_time']);
                $list['video_duration'] = gmstrftime("%H:%M:%S", $list['video_duration']);
            }
        }
        return $this->writeJson(Status::CODE_OK, "OK", $data);
    }

    /**
     * 从缓存读取数据
     */
    public function listsCache() {
        $catId = !empty($this->params['cat_id']) ? intval($this->params['cat_id']) : 0;
        try {
            $videoData = (new VideoModel())->getCache($catId);
        } catch (\Exception $e) {
            return $this->writeJson(Status::CODE_BAD_REQUEST, '请求失败');
        }
        $count = count($videoData);
        return $this->writeJson(Status::CODE_OK, "ok", $this->getPaginateData($count, $videoData));
    }
}
