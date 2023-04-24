<?php

namespace App\ApiBackend\Menu;

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
        if (request('id') > 0) {
            $ruleArr['name'] = "required";
        } else {
            $ruleArr['name'] = "required|unique:admin_menu";
        }
        $messageArr = [];
        //验证
        $validator = Validator::make(request()->all(), $ruleArr, $messageArr, [
            "name" => "菜单名称"
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
        $isUpdate = $deptId = request('id');
        //数据处理
        $insertData = [
            "title" => request('title'),
            "name" => request('name'),
            "path" => request('path'),
            "pid" => $parentId,
            "perms" => request('perms'),
            "type" => request('type'),
            "sort" => request('sort'),
            "status" => request('status'),
            "icon" => request('icon'),
            "isHide" => request('isHide'),
            "isLink" => request('isLink') ?? 0,
        ];
        $insertData['pids'] = $parentId;
        $pids = DB::table('admin_menu')->where('id', $parentId)->value('pids');
        if($pids)
        {//上级目录
            $insertData['pids'] = $pids.','.$parentId;
        }

        switch (request('type'))
        {
            case 0:
                $insertData['component'] = "LAYOUT";
                break;
            case 1:
                $insertData['component'] = request('component');
                break;
        }
        //更新数据
        try {
            if ($isUpdate) {//更新
                $insertData['updateTime'] = date('Y-m-d H:i:s');
                DB::table('admin_menu')->where('id', $deptId)->update($insertData);
                $this->result["message"] = "更新菜单成功";

            } else {//新增
                $insertData['createTime'] = date('Y-m-d H:i:s');
                DB::table('admin_menu')->insert($insertData);
                $this->result["message"] = "新增菜单成功";
            }

        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] = "错误: " . $exception->getMessage();
        }
        return $this->result;

    }
}
