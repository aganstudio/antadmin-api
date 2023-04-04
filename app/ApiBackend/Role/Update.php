<?php

namespace App\ApiBackend\Role;

use App\Http\Controllers\ApiBaseController;
use App\Jobs\AdminOperateLogJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  更新角色
 */
class Update extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {

        try {
            request()->validate([
                'roleId' => 'required',
                'name' => 'required',
                'label' => 'required',
                // 'remark' => 'required',
                'menuList' => 'required|array',
            ]);
        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] = "参数错误: " . $exception->getMessage();
        }

    }

    /**
     * 业务主体
     */
    protected function service()
    {
        $roleId = \request('roleId');
        $menuList = \request('menuList');
        //数据
        $updateData = [
            "name" => request('name'),
            "label" => request('label'),
            "remark" => request('remark', ""),
            "created_at" => date('Y-m-d H:i:s'),
        ];
        $roleMenuUpdateData = [];
        $roleMenuList = DB::table('sys_role_menu')->where("role_id", $roleId)->pluck('menu_id');
        if (md5(implode(",", $roleMenuList->toArray())) != md5(implode(",", $menuList))) {
            foreach ($menuList as $val) {
                $roleMenuUpdateData[] = [
                    'role_id' => $roleId,
                    'menu_id' => $val,
                ];
            }
        }
        //更新数据
        try {
            DB::table('sys_role')->where('id', $roleId)->update($updateData);
            if ($roleMenuUpdateData) {
                //删除role-menu原先
                DB::table('sys_role_menu')->where('role_id', $roleId)->delete();
                DB::table('sys_role_menu')->insert($roleMenuUpdateData);
            }

        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "更新数据: " . $exception->getMessage();
        }

        $requestArr = [
            'url'=>\request()->url(),
            'path'=>\request()->path(),
            'all'=>\request()->all(),
            'ip'=>\request()->ip(),
            'userAgent'=>\request()->header("user-agent"),
        ];
        AdminOperateLogJob::dispatch(\request()->user(),$requestArr,$this->result, "修改角色");

        return $this->result;
    }
}
