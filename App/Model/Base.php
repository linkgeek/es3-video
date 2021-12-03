<?php

namespace App\Model;

use EasySwoole\Pool\Manager;

/**
 * Model 基类
 * Class Base
 * @package App\Model
 */
class Base {
    public $db = null;
    public $client = null;

    public function __construct() {
        if (empty($this->tableName)) {
            throw new \Exception('table error');
        }
        $this->client = Manager::getInstance()->get('db1')->getObj();
        //查询 video 表中所有的数据
        $this->db = $this->client->queryBuilder();
    }

    public function add($data) {
        if (empty($data) || !is_array($data)) {
            return false;
        }
        $this->db->insert($this->tableName, $data);
        $res = $this->client->execBuilder();
        return $res;
    }

    public function getById($id) {
        $id = intval($id);
        if (empty($id)) {
            return [];
        }
        $this->db->where("id", $id);
        $result = $this->db->getOne($this->tableName);
        return $result ?? [];
    }
}