<?php

namespace App\Lib\Upload;

use App\Lib\Utils;

/**
 * upload 基类
 * @package App\Lib\Upload
 */
class Base {

    public $request = null;
    public $type = ''; //上传文件file-key，value: image|video|txt
    public $file = null;
    public $size = null;
    public $clientMediaType = null;

    public function __construct($request, $type = null) {
        $this->request = $request;
        if (empty($type)) {
            $files = $this->request->getSwooleRequest()->files;
            $types = array_keys($files);
            $this->type = $types[0];
        } else {
            $this->type = $type;
        }
    }

    /**
     * 上传文件
     * @return bool
     * @throws \Exception
     */
    public function upload() {
        if ($this->type != $this->fileType) {
            return false;
        }
        $up_file = $this->request->getUploadedFile($this->type);
        $this->size = $up_file->getSize();
        $this->checkSize();
        $fileName = $up_file->getClientFileName();
        $this->clientMediaType = $up_file->getClientMediaType();
        $this->checkMediaType();
        $file = $this->getFile($fileName);
        $flag = $up_file->moveTo($file);
        if (!empty($flag)) {
            return $this->file;
        }
        return false;
    }

    /**
     * 生成文件保存路径
     * @param $fileName
     * @return string
     */
    public function getFile($fileName) {
        $pathInfo = pathinfo($fileName);
        $extension = $pathInfo['extension'];
        $dirname = "/" . $this->type . "/" . date("Y") . "/" . date("m");
        $dir = EASYSWOOLE_ROOT . "/public" . $dirname;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $basename = "/" . Utils::getFileKey($fileName) . "." . $extension;
        $this->file = $dirname . $basename;
        return $this->file;
    }

    /**
     * 检查上传文件类型
     * @return bool
     * @throws \Exception
     */
    public function checkMediaType() {
        $clientMediaType = explode("/", $this->clientMediaType);
        $clientMediaType = $clientMediaType[1] ?? "";
        if (empty($clientMediaType)) {
            throw new \Exception("上传{$this->type}文件不合法");
        }
        if ($this->fileExtTypes && !in_array($clientMediaType, $this->fileExtTypes)) {
            throw new \Exception("上传{$this->type}文件类型不允许");
        }
        return true;
    }

    /**
     * 检查文件大小
     * @return bool
     * @throws \Exception
     */
    public function checkSize() {
        if (empty($this->size)) {
            return false;
        }
        // 大小限制
        if ($this->size > $this->maxSize) {
            throw new \Exception("上传{$this->type}文件超过{$this->maxSize}M");
        }
        return true;
    }
}