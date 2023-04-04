<?php

namespace App\ApiBackend\Menu;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

/**
 *  获取全部菜单
 */
class SelectList extends ApiBaseController
{

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
        //基础查询构造器
        $selectFields = [
            "id",
            "pid",
            "name",
        ];
        $listData =  DB::table('admin_menu')
            ->select($selectFields)
            ->where('pid',0)
            ->where('type',"<",2)
            ->orderBy("sort", "ASC")
            ->get();
        foreach ($listData as &$item)
        {
            $item->children = DB::table('admin_menu')
                ->select($selectFields)
                ->where('pid',$item->id)
                ->where('type',"<",2)
                ->orderBy("sort", "ASC")
                ->get();
            foreach ($item->children as &$childItem)
            {
                $childItem->children = DB::table('admin_menu')
                    ->select($selectFields)
                    ->where('pid',$childItem->id)
                    ->where('type',"<",2)
                    ->orderBy("sort", "ASC")
                    ->get();
            }
        }

        empty($listData) && $listData = [];

        $this->result["data"] = $listData;
        return $this->result;
    }
}
