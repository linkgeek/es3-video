<?php

namespace App\Model;

use App\Lib\Pool\RedisPool;
use EasySwoole\FastCache\Cache;

class Video extends Base {
    public $tableName = 'video';

    public function getVideoData($condition = [], $page = 1, $size = 10) {
        if (!empty($condition['cat_id'])) {
            $this->db->where("cat_id", $condition['cat_id']);
        }
        $this->db->where("status", 1);
        if (!empty($size)) {
            $this->db->pageLimit = $size;
        }
        $this->db->orderBy("id", "desc");
        $res = $this->db->withTotalCount()->get($this->tableName, [($page - 1) * $size, $size], '*');
        $data = [
            'total_page' => ceil($this->db->getTotalCount() / $size),
            'page_size'  => $size,
            'count'      => $this->db->getTotalCount(),
            'lists'      => $res,
        ];
        return $data;
    }

    /**
     * 设置首页缓存
     */
    public function setIndexVideo() {
        $catIds = array_keys(\Yaconf::get("category.cats"));
        array_unshift($catIds, 0);
        $cacheType = \Yaconf::get("base.indexCacheType");
        foreach ($catIds as $catId) {
            $condition = [];
            if (!empty($catId)) {
                $condition['cat_id'] = $catId;
            }
            try {
                $data = self::getVideoCacheData($condition);
            } catch (\Exception $e) {
                $data = [];
            }
            if (empty($data)) {
                continue;
            }
            foreach ($data as &$list) {
                $list['create_time'] = date("Ymd H:i:s", $list['create_time']);
                $list['video_duration'] = gmstrftime("%H:%M:%S", $list['video_duration']);
            }
            switch ($cacheType) {
                case "file":
                    $res = file_put_contents(self::getVideoCatIdFile($catId), json_encode($data));
                    break;
                case "table":
                    $res = Cache::getInstance()->set($this->getCatKey($catId), $data);
                    break;
                case "redis":
                    $redis = RedisPool::defer();
                    $res = $redis->set($this->getCatKey($catId), json_encode($data));
                    break;
                default:
                    throw new \Exception("请求不合法");
                    break;
            }
            if (empty($res)) {
                // 记录 报警
            }
        }
    }

    /**
     * 获取缓存数据
     * @param array $condition
     * @param int $size
     * @return mixed
     */
    public function getVideoCacheData($condition = [], $size = 1000) {
        if (!empty($condition['cat_id'])) {
            $this->db->where("cat_id", $condition['cat_id']);
        }
        // 获取正常的内容
        $this->db->where("status", 1);
        if (!empty($size)) {
            $this->db->pageLimit = $size;
        }
        $this->db->orderBy("id", "desc");
        $res = $this->db->withTotalCount()->get($this->tableName, [0, $size], '*');
        return $res;
    }

    /**
     * 获取首页缓存
     * @param int $catId
     * @return array|false|mixed|string
     * @throws \Exception
     */
    public function getCache($catId = 0) {
        $cacheType = \Yaconf::get("base.indexCacheType");
        switch ($cacheType) {
            case "file":
                $videoFile = self::getVideoCatIdFile($catId);
                $videoData = is_file($videoFile) ? file_get_contents($videoFile) : [];
                $videoData = !empty($videoData) ? json_decode($videoData, true) : [];
                break;
            case "table":
                $videoData = Cache::getInstance()->get($this->getCatKey($catId));
                $videoData = !empty($videoData) ? $videoData : [];
                break;
            case "redis":
                $redis = RedisPool::defer();
                $videoData = $redis->get($this->getCatKey($catId));
                $videoData = !empty($videoData) ? json_decode($videoData, true) : [];
                break;
            default:
                throw new \Exception('请求不合法');
                break;
        }
        return $videoData;
    }

    /**
     * 根据catId获取文件
     * @param int $catId
     * @return string
     */
    public function getVideoCatIdFile($catId = 0) {
        $json_file = EASYSWOOLE_ROOT . "/public/video/json/" . $catId . ".json";
        $position = strrpos($json_file, '/');
        $path = substr($json_file, 0, $position);
        if (!file_exists($path)) { // 路径不存在，则进行创建
            mkdir($path, 0777, true);
        }
        return $json_file;
    }

    /**
     * 获取缓存的key
     * @param int $catId
     * @return string
     */
    public function getCatKey($catId = 0) {
        return "index_video_data_cat_id_" . $catId;
    }
}
