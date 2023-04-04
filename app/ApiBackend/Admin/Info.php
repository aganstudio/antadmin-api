<?php

namespace App\ApiBackend\Admin;

use App\Dao\RoleDao;
use App\Dao\AdminDao;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  获取用户信息
 */
class Info extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {
    }

    /**
     * 业务主体
     */
    protected function service(): array
    {
        $uid = \request('uid') ? \request('uid') : \request()->user()['id'];
        $userInfo = AdminDao::getAdminInfo($uid);
        if (!$userInfo) {
            $this->result['code'] = 1;
            $this->result['message'] = '用户数据获取失败';
            return $this->result;
        }
        //角色
        // $userInfo->roleNames = [];
        // if($userInfo->roleIds)
        // {
        //     $roleList = DB::table("sys_role")
        //         ->whereIn("id",explode(",",$userInfo->roleIds))
        //         ->pluck("name");
        //     !empty($roleList) && $userInfo->roleNameArr = $roleList->toArray();
        // }

        $userInfo->loginIp = \request()->ip();

        $this->result['data'] = $userInfo;
        return $this->result;
    }
}
