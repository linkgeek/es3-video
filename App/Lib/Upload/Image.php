<?php

namespace App\Lib\Upload;

/**
 * Class Image
 * @package App\Lib\Upload
 */
class Image extends Base {
    public $fileType = "image";
    public $maxSize = 122;

    public $fileExtTypes = [
        "png",
        "jpeg",
    ];
}