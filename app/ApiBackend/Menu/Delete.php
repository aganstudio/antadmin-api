<?php

namespace App\ApiBackend\Menu;

use App\Dao\MenuDao;
use App\Dao\RoleDao;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  删除菜单
 */
class Delete extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {
        try {
            request()->validate([
                'menuId' => 'required',
            ]);
        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] = "参数错误: " . $exception->getMessage();
        }
    }

    /**
     * 业务主体
     */
    protected function service()
    {
        //删除数据
        try {
            DB::table('admin_menu')->where('id', request('menuId'))->delete();
        } catch (\Exception $exception) {
            $this->result["code"] = 2;
            $this->result["message"] = "删除数据: " . $exception->getMessage();
        }
        $this->result["message"] = "删除成功";

        return $this->result;
    }
}
