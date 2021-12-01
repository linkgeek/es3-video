<?php

namespace App\Model;

use App\Utility\Pool\MysqlPool;

/**
 * Model 基类
 * Class Base
 * @package App\Model
 */
class Base {
    public $db = null;

    public function __construct() {
        if (empty($this->tableName)) {
            throw new \Exception('table error');
        }
        $this->db = MysqlPool::defer();
    }

    public function add($data) {
        if (empty($data) || !is_array($data)) {
            return false;
        }
        return $this->db->insert($this->tableName, $data);
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