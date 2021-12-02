<?php

namespace App\HttpController\Api;

use EasySwoole\Http\AbstractInterface\Controller;

class Base extends Controller {
    public $params = [];

    public function index() {
        // TODO: Implement index() method.
    }

    public function onRequest(?string $action): ?bool {
        $this->getParams();
        return true;
    }

    public function getParams() {
        $params = $this->request()->getRequestParam();
        $params['page'] = !empty($params['page']) ? intval($params['page']) : 1;
        $params['size'] = !empty($params['size']) ? intval($params['size']) : 5;
        $params['from'] = ($params['page'] - 1) * $params['size'];
        $this->params = $params;
    }

    /**
     * @param $total
     * @param $data
     * @param int $isSplice
     * @return array
     */
    public function getPaginateData($total, $data, $isSplice = 1) {
        $totalPage = ceil($total / $this->params['size']);
        // 限制深度分页
        $maxPageSize = \Yaconf::get("base.maxPageSize");
        if ($totalPage > $maxPageSize) {
            $totalPage = $maxPageSize;
        }
        $data = $data ?? [];
        if ($isSplice == 1) {
            $data = array_splice($data, $this->params['from'], $this->params['size']);
        }
        return [
            'total_page' => $totalPage,
            'page_size'  => $this->params['size'],
            'count'      => intval($total),
            'lists'      => $data
        ];
    }

    /**
     * @param int $statusCode
     * @param string $message
     * @param array $result
     * @return bool
     */
    protected function writeJson($statusCode = 200, $message = "", $result = []) {
        if (!$this->response()->isEndResponse()) {
            $data = [
                'code'    => $statusCode,
                'message' => $message,
                'result'  => $result
            ];
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            trigger_error('response has end');
            return false;
        }
    }
}