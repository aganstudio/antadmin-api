<?php

namespace App\ApiBackend\Admin;

use App\Dao\DepartmentDao;
use App\Dao\UserDao;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 *  添加用户信息
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
                'username' => 'required',
                'name' => 'required',
                'deptId' => 'required',
                'roleId' => 'required',
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
        $isUpdate = $adminId = request('id');

        //数据
        $insertData = [
            "username" => request('username'),
            "name" => request('name'),
            "deptId" => request('deptId'),
            "roleId" => request('roleId'),
            "remark" => request('remark', ""),
            "createTime" => date('Y-m-d H:i:s'),
        ];
        //更新数据
        try {
            if ($isUpdate) {//更新
                $insertData['updateTime'] = date('Y-m-d H:i:s');
                DB::table('admin')->where('id', $adminId)->update($insertData);
                $this->result["message"] = "更新账户成功";

            } else {//新增
                $insertData['password'] = Hash::make(\request('password'));
                $insertData['createTime'] = date('Y-m-d H:i:s');
                DB::table('admin')->insert($insertData);
                $this->result["message"] = "新增账户成功";
            }

        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] = "错误: " . $exception->getMessage();
        }
        return $this->result;
    }
}
