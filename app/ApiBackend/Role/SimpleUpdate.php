<?php

namespace App\ApiBackend\Role;

use App\Http\Controllers\ApiBaseController;
use App\Jobs\AdminOperateLogJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  更新角色
 */
class SimpleUpdate extends ApiBaseController
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
                'value' => 'required',
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
        //数据
        $updateData = [
            request('name')=>request('value')
        ];
        //更新数据
        try {
            DB::table('admin_role')->where('roleId', $roleId)->update($updateData);
        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "更新数据: " . $exception->getMessage();
        }

        return $this->result;
    }
}
