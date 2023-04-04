<?php

namespace App\ApiBackend\Department;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 *  删除部门
 */
class Delete extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {

        // 定义规则
        $ruleArr = [
            'deptId' => 'required',
        ];
        $messageArr = [];
        //验证
        $validator = Validator::make(request()->all(), $ruleArr);
        //验证失败
        if ($validator->stopOnFirstFailure()->fails()) {
            $this->result["code"] = 1;
            $this->result["message"] = "数据验证: " . $validator->errors()->first();
        }
        // 有下级部门 todo
        // 部门下有用户
        if (DB::table("admin")->where("deptId", request("deptId"))->count()) {
            $this->result["code"] = 9;
            $this->result["message"] = "数据验证: 该部门有关联用户";
        }
    }

    /**
     * 业务主体
     */
    protected function service()
    {
        try {
            DB::table('admin_department')->where('deptId', request("deptId"))->delete();

        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "删除失败: " . $exception->getMessage();
        }
        $this->result["message"] = "删除成功";
        return $this->result;
    }
}
