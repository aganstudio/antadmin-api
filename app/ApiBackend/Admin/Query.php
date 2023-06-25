<?php

namespace App\ApiBackend\Admin;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 *  获取全部用户信息
 */
class Query extends ApiBaseController
{
    private array $searchMap = [
        "username" => ["su.username", "="],
        "name" => ["su.name", "like"],
        "status" => ["su.status", "="],
        "deptId" => ["find_in_set(?,`sd`.`pids`) or sd.deptId=?", "raw", 2],
    ];

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
            "su.id",
            "su.username",
            "su.name",
            "su.deptId",
            "su.roleId",
            "su.status",
            "su.remark",
            "sd.deptName",
            "sr.roleName",
            "su.lastLoginTime",
        ];
        $queryConstructor = DB::table('admin as su')
            ->join('admin_role as sr', 'sr.roleId', '=', 'su.roleId')
            ->join('admin_department as sd', 'sd.deptId', '=', 'su.deptId')
            ->select($selectFields)
            ->orderBy("su.name", "ASC");

        //搜索条件
        $this->querySearchConstructor($queryConstructor, $this->searchMap);

        //是否需要分页
        $isPage = strpos(request()->path(), "page") ? 1 : 0;
        if ($isPage) {// 分页查询
            $total = $queryConstructor->count();
            $pageNumber = \request("pageNumber", 1);
            $pageSize = \request("pageSize", 20);
            $queryConstructor->offset(($pageNumber - 1) * $pageSize)->limit($pageSize);
        }

        $listData = $queryConstructor->get();
        empty($listData) && $listData = [];

        //返回结果
        if ($isPage) {// 分页查询
            $this->result["data"]["list"] = $listData;
            $this->result["data"]["total"] = $total;
        } else {
            $this->result["data"] = $listData;
        }
        return $this->result;

    }
}
