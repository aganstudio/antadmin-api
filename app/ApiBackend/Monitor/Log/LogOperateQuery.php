<?php

namespace App\Http\Controllers\Monitor\Log;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;


/**
 *  获取全部用户信息
 */
class LogOperateQuery extends ApiBaseController
{
    private array $searchMap = [
        "username" => ["aa.username", "="],
        "ip" => ["aa.ip", "="],
        "userAgent" => ["aa.user_agent", "like"],
        "startTime" => ["aa.created_at", "gt"],
        "endTime" => ["aa.created_at", "lt"],
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
            "aa.id",
            "aa.username",
            "aa.ip",
            "aa.route",
            "aa.url",
            "aa.params",
            "aa.response",
            "aa.remark",
            "aa.user_agent as userAgent",
            "aa.created_at as createdAt",
        ];
        $queryConstructor = DB::table('sys_operate_log as aa')
            ->select($selectFields)
            ->orderBy("aa.id", "DESC");
        $totalConstructor  =  DB::table('sys_operate_log as aa');

        //搜索条件
        foreach ($this->searchMap as $requestKey => $searchRule) {
            $requestKeyVal = \request($requestKey);
            if ($requestKeyVal) {
                switch ($searchRule[1]) {
                    case "=":
                        $queryConstructor->where($searchRule[0], $requestKeyVal);
                        $totalConstructor->where($searchRule[0], $requestKeyVal);
                        break;
                    case "gt":
                        $queryConstructor->where($searchRule[0],">", $requestKeyVal);
                        $totalConstructor->where($searchRule[0],">", $requestKeyVal);
                        break;
                    case "lt":
                        $queryConstructor->where($searchRule[0],"<", $requestKeyVal);
                        $totalConstructor->where($searchRule[0],"<", $requestKeyVal);
                        break;
                    case "like":
                        $queryConstructor->where($searchRule[0], "like", "%{$requestKeyVal}%");
                        $totalConstructor->where($searchRule[0], "like", "%{$requestKeyVal}%");
                        break;
                    case "raw":
                        $queryConstructor->whereRaw($searchRule[0], [$requestKeyVal]);
                        $totalConstructor->whereRaw($searchRule[0], [$requestKeyVal]);
                        break;
                }
            }
        }
        //分页
        $pageNumber = \request("pageNumber", 1);
        $pageSize = \request("pageSize", 20);
        $queryConstructor->offset(($pageNumber - 1) * $pageSize)->limit($pageSize);

        $listData = $queryConstructor->get();
        empty($listData) && $listData = [];
        $total = $totalConstructor->count();
        //返回结果
        $this->result["data"]["list"] = $listData;
        $this->result["data"]["pagination"] = ['total' => $total, 'page' => $pageNumber, 'size' => $pageSize];
        return $this->result;

    }
}
