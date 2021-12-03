<?php

namespace App\Model\Es;

use EasySwoole\Component\Di;

/**
 * Es 基础类
 * Class EsBase
 * @package App\Model\Es
 */
class EsBase {

    public $esClient = null;

    public function __construct() {
        $this->esClient = Di::getInstance()->get('ES');
    }

    /**
     * @param $name
     * @param integer $from 分页from
     * @param integer $size 分页size
     * @param string $type
     * @return array
     */
    public function searchByName($name, $from, $size, $type = 'match') {
        if (empty($name)) {
            return [];
        }

        $param = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                'query' => [
                    $type => [
                        'name' => $name
                    ],
                ],
                'from'  => $from,
                'size'  => $size,
            ],
        ];

        $res = $this->esClient->search($param);
        return $res;
    }

    public function insertByName($id, $data) {
        if (!$id || empty($data)) {
            return [];
        }

        $param = [
            'index' => $this->index,
            'type'  => $this->type,
            'id'    => $id,
            'body'  => $data,
        ];

        $res = $this->esClient->create($param);
        return $res;
    }

}