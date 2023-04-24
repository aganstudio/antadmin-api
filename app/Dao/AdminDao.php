<?php

namespace App\Dao;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 用户模块Dao类
 */
class AdminDao
{

    /**
     * 获取指定用户信息
     * @param int $uid
     * @return object|null
     */
    public static function getAdminInfo(int $uid): ?object
    {
        $selectFields = [
            "su.username",
            "su.name",
            "su.roleId",
            "su.isSuper",
            "sd.deptName",
        ];
        return DB::table("admin as su")
            ->leftJoin("admin_department as sd", "su.deptId", "=", "sd.deptId")
            ->where("su.id", $uid)
            ->where("su.status", "=",1)
            ->select($selectFields)
            ->first();
    }

    /**
     * 获取指定用户信息带缓存
     * @param int $uid
     * @param int $cacheSeconds
     * @return object|null
     */
    public static function getAdminInfoCache(int $uid, int $cacheSeconds = 0): ?object
    {
        $cacheKey = "adminer_{$uid}";
        $userInfo = Cache::get($cacheKey);
        if (null === $userInfo) {//未缓存
            $userInfo = self::getAdminInfo($uid);
            if ($userInfo) {// 有数据则缓存
                $cacheSeconds > 0 ? Cache::put($cacheKey, $userInfo, $cacheSeconds) : Cache::forever($cacheKey, $userInfo);
            }
        }
        return $userInfo;
    }


}
