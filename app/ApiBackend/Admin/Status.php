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
class Status extends ApiBaseController
{


    /**
     * 参数检查
     */
    protected function check()
    {
        try {
            request()->validate([
                'id' => 'required',
                'status' => 'required',
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
        //更新数据
        try {
            DB::table('admin')->where('id', request('id'))->update([
                'status'=>request('status')
            ]);
        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "更新失败: " . $exception->getMessage();
        }
        $this->result["message"] = "更改状态成功";
        return $this->result;

    }
}
