<?php

namespace App\HttpController\Api;

use App\Model\Es\EsVideo;
use EasySwoole\Http\Message\Status;

/**
 * Class Search
 * @package App\HttpController\Api
 */
class Search extends Base {

    /**
     * 搜索API
     */
    public function index() {
        $keyword = trim($this->params['keyword']);
        if (empty($keyword)) {
            return $this->writeJson(Status::CODE_OK, 'ok', $this->getPaginateData(0, [], 0));
        }

        $result = (new EsVideo())->searchByName($keyword, $this->params['from'], $this->params['size']);
        if (empty($result)) {
            return $this->writeJson(Status::CODE_OK, 'ok', $this->getPaginateData(0, [], 0));

        }

        $total = $result['hits']['total']['value'];
        if ($total == 0) {
            return $this->writeJson(Status::CODE_OK, 'ok', $this->getPaginateData(0, [], 0));

        }

        $resData = [];
        $hits = $result['hits']['hits'];
        foreach ($hits as $hit) {
            $source = $hit['_source'];
            $resData[] = [
                'id'             => $hit['_id'],
                'name'           => $source['name'],
                'image'          => $source['image'],
                'uploader'       => $source['uploader'],
                'video_duration' => '',
                'create_time'    => '',
                'keyword'        => [$keyword],
            ];
        }

        return $this->writeJson(Status::CODE_OK, 'ok', $this->getPaginateData($total, $resData, 0));
    }
}