<?php

namespace App\ApiBackend\Department;

use App\Dao\DepartmentDao;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  获取部门信息
 */
class Query extends ApiBaseController
{


    /**
     * 参数检查
     */
    protected function check()
    {
    }

    /**
     * 业务主体
     */
    protected function service()
    {
        //基础查询构造器
        $selectFields = [
            "aa.deptId",
            "aa.deptName",
            "aa.pid",
            "aa.sort",
            "aa.status",
            "aa.remark",
            "aa.createTime",
        ];
        $listData = DB::table('admin_department as aa')
            ->select($selectFields)
            ->where("aa.pid",0)
            ->orderBy("aa.sort", "ASC")
            ->get();
        foreach ($listData as &$val)
        {
            // $val->disabled = true;
            $val->children = DB::table('admin_department as aa')
                ->select($selectFields)
                ->where("aa.pid",$val->deptId)
                ->orderBy("aa.sort", "ASC")
                ->get();
        }

        //返回结果
        empty($listData) && $listData = [];
        $this->result["data"] = $listData;
        return $this->result;

    }
}
