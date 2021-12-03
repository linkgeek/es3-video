<?php

namespace App\Lib\Upload;

/**
 * Class Txt
 * @package App\Lib\Upload
 */
class Txt extends UploadBase {
    public $fileType = "txt";
    public $maxSize = 1 * 1024 * 1024;
}