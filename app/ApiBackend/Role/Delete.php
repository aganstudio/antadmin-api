<?php

namespace App\ApiBackend\Role;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

/**
 *  删除角色
 */
class Delete extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {
        try {
            request()->validate([
                'roleIds' => 'required|array',
            ]);
        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] = "参数错误: " . $exception->getMessage();
        }
        // 角色下有用户
        if (DB::table("admin")->whereIn("roleId", request("roleIds"))->count()) {
            $this->result["code"] = 9;
            $this->result["message"] = "该角色有关联用户, 请先删除用户";
        }

    }

    /**
     * 业务主体
     */
    protected function service()
    {

        $roleIdArr = \request('roleIds');
        try {
            DB::table('admin_role')->whereIn('roleId', $roleIdArr)->delete();
            DB::table('admin_role_menu')->whereIn('roleId', $roleIdArr)->delete();

        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "删除失败: " . $exception->getMessage();
        }
        $this->result["message"] = "删除成功";
        return $this->result;
    }
}
