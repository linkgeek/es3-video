<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019/8/26
 * Email: zhangatle@gmail.com
 */

namespace App\Lib\Upload;

class Image extends Base
{
    public $fileType = "image";
    public $maxSize = 122;

    public $fileExtTypes = [
        "png",
        "jpeg",
    ];
}