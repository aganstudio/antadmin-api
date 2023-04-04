<?php

namespace App\ApiBackend\Menu;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

/**
 *  获取全部菜单
 */
class Query extends ApiBaseController
{

    private array $searchMap = [
        "name" => ["aa.goodsNo", "like"],
    ];
    /**
     * 参数检查
     */
    protected function check()
    {

    }

    /**
     * 业务主体
     * 最大深度为4级
     */
    protected function service()
    {
        $typeTextArr = [
            0=>"目录",
            1=>"菜单",
            2=>"权限",
        ];
        //基础查询构造器
        $selectFields = [
            "id",
            "pid",
            "name",
            "component",
            "perms",
            "type",
            "icon",
            "sort",
            "keepalive",
            "isShow",
            "isLink",
            "status",
            "updateTime",
        ];
        $listData =  DB::table('admin_menu')
            ->select($selectFields)
            ->where('pid',0)
            ->orderBy("sort", "ASC")
            ->get();
        foreach ($listData as &$item)
        {
            $item->typeText = $typeTextArr[$item->type];
            $item->children = DB::table('admin_menu')
                ->select($selectFields)
                ->where('pid',$item->id)
                ->orderBy("sort", "ASC")
                ->get();
            foreach ($item->children as &$childItem)
            {
                $childItem->typeText = $typeTextArr[$childItem->type];
                $childItem->children = DB::table('admin_menu')
                    ->select($selectFields)
                    ->where('pid',$childItem->id)
                    ->orderBy("sort", "ASC")
                    ->get();

                foreach ($childItem->children as &$childOneItem)
                {
                    $childOneItem->typeText = $typeTextArr[$childOneItem->type];
                    $childOneItem->children = DB::table('admin_menu')
                        ->select($selectFields)
                        ->where('pid',$childOneItem->id)
                        ->orderBy("sort", "ASC")
                        ->get();
                    foreach ($childOneItem->children as &$childTwoItem)
                    {
                        $childTwoItem->typeText = $typeTextArr[$childTwoItem->type];
                    }
                }
            }
        }

        empty($listData) && $listData = [];

        $this->result["data"] = $listData;
        return $this->result;
    }
}
