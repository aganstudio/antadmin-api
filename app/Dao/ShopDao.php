<?php

namespace App\Dao;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 用户模块Dao类
 */
class ShopDao
{

    /**
     * 获取指定用户信息
     * @param int $uid
     * @return object|null
     */
    public static function getShopInfo(int $uid): ?object
    {
        $selectFields = [
            "su.id as shopId",
            "su.username",
            "su.name",
        ];
        return DB::table("shop as su")
            ->where("su.id", $uid)
            ->select($selectFields)
            ->first();
    }

}
