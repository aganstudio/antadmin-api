<?php

namespace App\ApiBackend\Role;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  获取全部权限数据
 */
class MenuQuery extends ApiBaseController
{
    private array $searchMap = [
        "roleId" => ["aa.roleId", "="],
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
        $queryConstructor = DB::table('admin_role_menu as aa')
            ->orderBy("aa.roleId", "ASC");
        //搜索条件
        $this->querySearchConstructor($queryConstructor, $this->searchMap);
        $listData = $queryConstructor->pluck("aa.menuId");
        empty($listData) && $listData = [];

        //返回结果
        $this->result["data"] = $listData;
        return $this->result;
    }
}
