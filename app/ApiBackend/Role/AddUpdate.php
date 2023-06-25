<?php

namespace App\ApiBackend\Role;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

/**
 *  添加角色
 */
class AddUpdate extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {
        try {
            request()->validate([
                'roleName' => 'required',
                // 'label' => 'required',
                'menu' => 'required|array',
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
        $menuList = \request('menu');
        $isUpdate = $roleId = request('id');
        //数据
        $insertData = [
            "roleName" => request('roleName'),
            // "label" => request('label'),
            "status" => request('status'),
            "remark" => request('remark', ""),
        ];

        //更新数据
        try {

            if ($isUpdate) {//更新
                $insertData['updateTime'] = date('Y-m-d H:i:s');
                DB::table('admin_role')->where('roleId', $roleId)->update($insertData);
                //删除原先权限
                DB::table('admin_role_menu')->where('roleId', $roleId)->delete();
                $this->result["message"] = "更新角色成功";

            } else {//新增
                $insertData['createTime'] = date('Y-m-d H:i:s');
                $roleId = DB::table('admin_role')->insertGetId($insertData);
                $this->result["message"] = "新增角色成功";
            }
            $roleMenuUpdateData = [];
            foreach ($menuList as $val) {
                $roleMenuUpdateData[] = [
                    'roleId' => $roleId,
                    'menuId' => $val,
                ];
            }
            if ($roleMenuUpdateData) {
                DB::table('admin_role_menu')->insert($roleMenuUpdateData);
            }

        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "更新数据: " . $exception->getMessage();
        }

        return $this->result;
    }
}
