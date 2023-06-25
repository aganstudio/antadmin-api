<?php

namespace App\ApiBackend\Workbench;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

//数据汇总
class Statistic extends ApiBaseController
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
        // dump(request()->user()->roleId);
        $roleId = request()->user()->roleId;
        $timeCur = time();
        //本周一
        $weekdayCur = date('w',$timeCur);
        $monday = date('Y-m-d', $timeCur - ($weekdayCur-1)*86400);

        switch ($roleId) {
            case 6:
                $supplierId = request()->user()->supplierId;
                $groupData[] = [
                    "title" => '商品数',
                    "icon" => 'material-symbols:database-outline',
                    "color" => 'green',
                    "action" => '本周',
                    "value" => DB::table("wms_goods")->where('createTime','>',$monday )->where('status',1)
                        ->where('supplierId',$supplierId)->count(),
                    "total" => DB::table("wms_goods")->where('status',1)
                        ->where('supplierId',$supplierId)->count(),
                ];

                break;
            default:
                $groupData[] = [
                    "title" => '商品数',
                    "icon" => 'material-symbols:database-outline',
                    "color" => 'green',
                    "action" => '本周',
                    "value" => DB::table("wms_goods")->where('createTime','>',$monday )->where('status',1)->count(),
                    "total" => DB::table("wms_goods")->where('status',1)->count(),
                ];
                $orderStatusArr = [0,2,3,4,6];
                $groupData[] = [
                    "title" => '订单数',
                    "icon" => 'material-symbols:receipt-long-outline',
                    "color" => 'green',
                    "action" => '本周',
                    "value" => DB::table("wms_order")->where('createTime','>',$monday )->whereIn('status',$orderStatusArr)->count(),
                    "total" => DB::table("wms_order")->whereIn('status',$orderStatusArr)->count(),
                ];
                $orderStatusArr = [4,6];
                $groupData[] = [
                    "title" => '出库量',
                    "icon" => 'material-symbols:warehouse-outline-rounded',
                    "color" => 'green',
                    "action" => '本周',
                    "value" => (int) DB::table("wms_order")->where('createTime','>',$monday )->whereIn('status',$orderStatusArr)->sum("buyNum"),
                    "total" => (int) DB::table("wms_order")->whereIn('status',$orderStatusArr)->sum("buyNum"),
                ];
                $groupData[] = [
                    "title" => '供应商',
                    "icon" => 'material-symbols:supervisor-account-outline-rounded',
                    "color" => 'green',
                    "action" => '本周',
                    "value" => DB::table("scm_supplier")->where('createTime','>',$monday )->count(),
                    "total" => DB::table("scm_supplier")->count(),
                ];
                break;

        }

        $this->result["data"] = $groupData;
        return $this->result;
    }
}
