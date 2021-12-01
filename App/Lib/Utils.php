<?php

namespace App\Lib;


class Utils {
    /**
     * 生成唯一性的key
     * @param $str
     * @return bool|string
     */
    public static function getFileKey($str) {
        return substr(md5(self::makeRandomString() . $str . time() . rand(0, 9999)), 8, 16);
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string|null
     */
    public static function makeRandomString($length = 1) {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }
}