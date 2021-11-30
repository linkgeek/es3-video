<?php
/**
 * Created by PhpStorm.
 * User: zhang
 * Date: 2019/8/26
 * Email: zhangatle@gmail.com
 */

namespace App\Lib\Upload;


class Video extends Base
{
    public $fileType = "video";
    public $maxSize = 122;

    public $fileExtTypes = [
        "mp4",
        "x-flv",
    ];
}