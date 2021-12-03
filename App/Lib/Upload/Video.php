<?php

namespace App\Lib\Upload;

/**
 * Class Video
 * @package App\Lib\Upload
 */
class Video extends UploadBase {
    public $fileType = "video";
    public $maxSize = 10 * 1024 * 1024; //10M

    public $fileExtTypes = [
        "mp4",
        "wmv",
        "x-flv",
    ];
}