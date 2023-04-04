<?php

namespace App\Utils;

class FunctionUtil
{
    /**
     * json_encode设置
     * @param $obj
     * @return false|string
     */
    public static function jsonEncode($obj)
    {
        return json_encode($obj, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
