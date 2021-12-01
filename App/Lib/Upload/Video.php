<?php

namespace App\Lib\Upload;

/**
 * Class Video
 * @package App\Lib\Upload
 */
class Video extends Base {
    public $fileType = "video";
    public $maxSize = 122;

    public $fileExtTypes = [
        "mp4",
        "x-flv",
    ];
}