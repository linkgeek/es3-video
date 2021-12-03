<?php

namespace App\HttpController\Api;

use App\Lib\ClassArr;

/**
 * 文件上传
 */
class Upload extends Base {

    /**
     * 文件上传处理
     * @return bool
     */
    public function file() {
        $request = $this->request();
        $files = $request->getSwooleRequest()->files;
        if (!$files) {
            return $this->writeJson(400, '上传文件不可为空');
        }
        $types = array_keys($files);
        $type = $types[0];
        if (empty($type)) {
            return $this->writeJson(400, '上传文件不合法');
        }

        // 使用PHP的反射机制
        try {
            $classObj = new ClassArr();
            $classStats = $classObj->uploadClassStat();
            $uploadObj = $classObj->initClass($type, $classStats, [$request, $type]);
            $file = $uploadObj->upload();
        } catch (\Exception $e) {
            return $this->writeJson(400, $e->getMessage(), []);
        }

        if (empty($file)) {
            return $this->writeJson(400, '上传失败', []);
        }

        $data = ['url' => $file];
        return $this->writeJson(200, 'OK', $data);
    }
}