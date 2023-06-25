<?php

namespace App\ApiBackend\Workbench;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

//数据汇总
class Project extends ApiBaseController
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

        $groupData = [];
        $roleId = request()->user()->roleId;
        $tagColorArr = ['pink', 'red', 'orange', 'green', 'cyan', 'blue', 'purple'];
        switch ($roleId) {
            case 6:
                $supplierId = request()->user()->supplierId;
                $count = DB::table("wms_goods_apply")->where('status','!=', 1)->where('supplierId',$supplierId)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待审商品',
                        "icon" => 'ant-design:audit-outlined',
                        "value" => $count,
                        "group" => '供应链管理',
                        "route" => '/wms/goodsapply/index',
                    ];
                }
                break;
            default:
                $count = DB::table("scm_supplier_apply")->where('status', 0)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待审供应商',
                        "icon" => 'material-symbols:assignment-ind-outline',
                        "value" => $count,
                        "group" => '供应链管理',
                        "route" => '/scm/supplierlist/supplierapply',
                    ];
                }
                $count = DB::table("wms_goods_apply")->where('status', 0)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待审商品',
                        "icon" => 'ant-design:audit-outlined',
                        "value" => $count,
                        "group" => '供应链管理',
                        "route" => '/wms/goodsapply/page',
                    ];
                }
                $count = DB::table("wms_goods")->where('status', 0)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待完善商品',
                        "icon" => 'mdi:timer-sand-complete',
                        "value" => $count,
                        "group" => '供应链管理',
                        "route" => '/scm/supplierlist/goods',
                    ];
                }
                $count = DB::table("wms_product")->where('status', 0)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待上架商品',
                        "icon" => 'material-symbols:assignment-turned-in-outline',
                        "value" => $count,
                        "group" => '供应链管理',
                        "route" => '/wms/stock/inventory',
                    ];
                }
                $count = DB::table("wms_order")->where('status', 2)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待分拣订单',
                        "icon" => 'mingcute:package-line',
                        "value" => $count,
                        "group" => '仓储管理',
                        "route" => '/wms/outstock/pickup',
                    ];
                }
                $count = DB::table("wms_order")->where('status', 3)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待发货订单',
                        "icon" => 'carbon:delivery',
                        "value" => $count,
                        "group" => '仓储管理',
                        "route" => '/wms/outstock/order',
                    ];
                }
                $count = DB::table("wms_puorder")->where('weStatus','!=', 2)->count();
                if ($count > 0) {
                    $groupData[] = [
                        "title" => '待完成采购单',
                        "icon" => 'bx:purchase-tag',
                        "value" => $count,
                        "group" => '仓储管理',
                        "route" => '/wms/purchase/puorder',
                    ];
                }
                break;
        }
        foreach ($groupData as $key => &$val) {
            $val['color'] = $tagColorArr[$key % 8];
        }


        $this->result["data"] = $groupData;
        return $this->result;
    }
}
