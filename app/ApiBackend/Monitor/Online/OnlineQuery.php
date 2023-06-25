<?php

namespace App\Http\Controllers\Monitor\Online;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;


/**
 *  获取全部用户信息
 */
class OnlineQuery extends ApiBaseController
{
    private array $searchMap = [
        "username" => ["su.username", "="],
        "ip" => ["aa.ip", "="],
        "departmentId" => ["aa.name", "like"],
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
            "su.id as uid",
            "su.username",
            "aa.ip",
            "aa.name as userAgent",
            "aa.last_used_at as lastUsedAt",
            "aa.created_at as createdAt",
        ];
        $queryConstructor = DB::table('personal_access_tokens as aa')
            ->join('sys_admin as su', 'su.id', '=', 'aa.tokenable_id')
            ->select($selectFields)
            ->orderBy("aa.last_used_at", "DESC");
        $totalConstructor = DB::table('personal_access_tokens as aa')
            ->join('sys_admin as su', 'su.id', '=', 'aa.tokenable_id');
        //搜索条件
        foreach ($this->searchMap as $requestKey => $searchRule) {
            $requestKeyVal = \request($requestKey);
            if ($requestKeyVal) {
                switch ($searchRule[1]) {
                    case "=":
                        $queryConstructor->where($searchRule[0], $requestKeyVal);
                        $totalConstructor->where($searchRule[0], $requestKeyVal);
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
        $total = $totalConstructor->count();
        $pageNumber = \request("pageNumber", 1);
        $pageSize = \request("pageSize", 20);
        $queryConstructor->offset(($pageNumber - 1) * $pageSize)->limit($pageSize);

        $listData = $queryConstructor->get();
        empty($listData) && $listData = [];
        //数据统一处理
        foreach ($listData as &$item) {
            $item->disable = $item->uid == 1 ? 1 : 0;
            $flagTime = time() - strtotime($item->lastUsedAt);
            if ($flagTime < 10 * 60) {
                $item->onlineFlag = "green,10分钟内";
            } elseif ($flagTime < 30 * 60) {
                $item->onlineFlag = "orange,30分钟内";
            } elseif ($flagTime < 60 * 60) {
                $item->onlineFlag = "blue,1小时内";
            } elseif ($flagTime < 24 * 60 * 60) {
                $item->onlineFlag = "red,1天内";
            } else {
                $item->onlineFlag = "grey,超1天";
            }
        }
        //返回结果
        $this->result["data"]["list"] = $listData;
        $this->result["data"]["pagination"] = ['total' => $total, 'page' => $pageNumber, 'size' => $pageSize];
        return $this->result;

    }
}
