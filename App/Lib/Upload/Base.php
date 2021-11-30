<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019/8/26
 * Email: zhangatle@gmail.com
 */

namespace App\Lib\Upload;
use App\Lib\Utils;

class Base
{
    public $type = '';

    public function __construct($request, $type = null)
    {
        $this->request = $request;
        if(empty($type)){
            $files = $this->request->getSwooleRequest()->files;
            $types = array_keys($files);
            $this->type = $types[0];
        }else{
            $this->type = $type;
        }
    }

    /**
     * 上传文件
     * @return bool
     * @throws \Exception
     */
    public function upload(){
        if($this->type != $this->fileType){
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
        if(!empty($flag)){
            return $this->file;
        }
        return false;
    }

    public function getFile($fileName){
        $pathinfo = pathinfo($fileName);
        $extension = $pathinfo['extension'];
        $dirname = "/".$this->type."/".date("Y")."/".date("m");
        $dir = EASYSWOOLE_ROOT."/webroot".$dirname;
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
        $basename = "/".Utils::getFileKey($fileName).".".$extension;
        $this->file = $dirname.$basename;
        return $dir.$basename;
    }

    /**
     * 检查上传文件类型
     * @return bool
     * @throws \Exception
     */
    public function checkMediaType(){
        $clientMediaType = explode("/",$this->clientMediaType);
        $clientMediaType = $clientMediaType[1] ?? "";
        if(empty($clientMediaType)){
            throw new \Exception("上传{$this->type}文件不合法");
        }
        if(!in_array($clientMediaType,$this->fileExtTypes)){
            throw new \Exception("上传{$this->type}文件不合法");
        }
        return true;
    }

    /**
     * 检查文件大小
     * @return bool
     */
    public function checkSize(){
        if(empty($this->size)){
            return false;
        }
    }
}