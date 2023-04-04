<?php

namespace App\ApiBackend\Department;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 *  添加部门
 */
class AddUpdate extends ApiBaseController
{


    /**
     * 参数检查
     */
    protected function check()
    {
        // 定义规则
        $ruleArr = [
            'pid' => 'required',
            'status' => 'required',
            'sort' => 'required',
        ];
        if (request('deptId') > 0) {
            $ruleArr['deptName'] = "required";
        } else {
            $ruleArr['deptName'] = "required|unique:admin_department";
        }
        $messageArr = [];
        //验证
        $validator = Validator::make(request()->all(), $ruleArr, $messageArr, [
            "deptName" => "部门名称"
        ]);
        //验证失败
        if ($validator->stopOnFirstFailure()->fails()) {

            $this->result["code"] = 1;
            $this->result["message"] = "数据验证: " . $validator->errors()->first();
        }
    }

    /**
     * 业务主体
     */
    protected function service()
    {
        $parentId = request('pid');
        $isUpdate = $deptId = request('deptId');
        //数据处理
        $insertData = [
            "deptName" => request('deptName'),
            "pid" => $parentId,
            "sort" => request('sort'),
            "status" => request('status'),
            "remark" => request('remark', ""),
        ];
        if ($parentId > 0) {
            $parentIds = DB::table('admin_department')->where('deptId', $parentId)->value('pids');
            $insertData['pids'] = $parentIds . "," . $parentId;
        } else {
            $insertData['pids'] = $parentId;
        }

        //更新数据
        try {
            if ($isUpdate) {//更新
                $insertData['updateTime'] = date('Y-m-d H:i:s');
                $id = DB::table('admin_department')->where('deptId', $deptId)->update($insertData);
                $this->result["message"] = "更新数据成功";

            } else {//新增
                $insertData['createTime'] = date('Y-m-d H:i:s');
                $id = DB::table('admin_department')->insert($insertData);
                $this->result["message"] = "新增部门成功";
            }

        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] = "错误: " . $exception->getMessage();
        }
        return $this->result;

    }
}
