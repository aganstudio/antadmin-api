<?php

namespace App\ApiBackend\Admin;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

/**
 *  更新用户信息
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
                'adminIds' => 'required|array',
            ]);
        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] = "参数错误: " . $exception->getMessage();
        }
        if (in_array(1, request('adminIds'))) {
            $this->result["code"] = 9;
            $this->result["message"] = "管理员admin禁止删除";
        }

    }

    /**
     * 业务主体
     */
    protected function service()
    {
        //删除数据
        try {
            DB::table('admin')->whereIn('id', request('adminIds'))->delete();
        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "删除数据: " . $exception->getMessage();
        }
        $this->result["message"] = "删除账户成功";
        return $this->result;
    }
}
