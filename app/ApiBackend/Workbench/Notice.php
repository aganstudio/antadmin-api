<?php

namespace App\ApiBackend\Workbench;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 *  获取全部用户信息
 */
class Notice extends ApiBaseController
{
    private array $searchMap = [
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
            "aa.*",
        ];
        $queryConstructor = DB::table('sys_notice as su')
            ->orderBy("su.id", "DESC");

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
