<?php

namespace App\ApiBackend\Role;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  获取全部权限数据
 */
class Query extends ApiBaseController
{
    private array $searchMap = [
        "roleName" => ["aa.roleName", "like"],
        "status" => ["aa.status", "="],
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
            "aa.roleId",
            "aa.roleName",
            "aa.label",
            "aa.status",
            "aa.remark",
            "aa.updateTime",
        ];
        $queryConstructor = DB::table('admin_role as aa')
            ->select($selectFields)
            ->orderBy("aa.roleId", "ASC");
        //搜索条件
        $this->querySearchConstructor($queryConstructor, $this->searchMap);

        //是否需要分页
        $isPage = strpos(request()->path(), "page") ? 1 : 0;
        if ($isPage) {// 分页查询
            $totalQuery = $queryConstructor;
            $pageNumber = \request("pageNumber", 1);
            $pageSize = \request("pageSize", 20);
            $queryConstructor->offset(($pageNumber - 1) * $pageSize)->limit($pageSize);
        }

        $listData = $queryConstructor->get();
        empty($listData) && $listData = [];

        //返回结果
        if ($isPage) {// 分页查询
            $total = $totalQuery->count();
            $this->result["data"]["list"] = $listData;
            $this->result["data"]["total"] = $total;
        } else {
            $this->result["data"] = $listData;
        }
        return $this->result;
    }
}
