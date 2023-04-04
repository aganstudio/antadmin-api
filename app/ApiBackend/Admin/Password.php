<?php

namespace App\ApiBackend\Admin;

use App\Dao\UserDao;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 *  更新用户密码
 */
class Password extends ApiBaseController
{


    /**
     * 参数检查
     */
    protected function check()
    {
        try {
            request()->validate([
                'id' => 'required',
                'password' => 'required',
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
        //数据
        $updateData = [
            "password" => Hash::make(\request('password')),
            "updateTime" => date('Y-m-d H:i:s'),
        ];

        //更新数据
        try {
            DB::table('admin')->where('id', request('id'))->update($updateData);
        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "更新失败: " . $exception->getMessage();
        }
        $this->result["message"] = "修改密码成功";
        return $this->result;

    }
}
