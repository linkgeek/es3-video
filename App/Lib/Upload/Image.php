<?php

namespace App\Lib\Upload;

/**
 * Class Image
 * @package App\Lib\Upload
 */
class Image extends UploadBase {
    public $fileType = "image";
    public $maxSize = 2 * 1024 * 1024;

    public $fileExtTypes = [
        "png",
        "jpeg",
    ];
}