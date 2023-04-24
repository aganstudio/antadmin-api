<?php

namespace App\ApiBackend\Admin;

use App\Dao\RoleDao;
use App\Dao\AdminDao;
use App\Http\Controllers\ApiBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  获取用户信息
 */
class MenuPermission extends ApiBaseController
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
    protected function service(): array
    {
        $uid = \request('uid') ? \request('uid') : \request()->user()['id'];
        $adminInfo = AdminDao::getAdminInfo($uid);
        if (!$adminInfo) {
            $this->result['code'] = 1;
            $this->result['message'] = '用户数据获取失败';
            return $this->result;
        }
        //菜单和权限
        $whereArr = [
            'status' => 1
        ];
        if (!$adminInfo->isSuper) {
            $whereArr['aa.roleId'] = $adminInfo->roleId;
            $roleMenuList = DB::table("admin_role_menu as aa")
                ->join("admin_menu as am", 'aa.menuId', '=', 'am.id')
                ->where($whereArr)
                ->get();
        } else {
            $roleMenuList = DB::table("admin_menu as aa")
                ->where($whereArr)
                ->orderBy("type", 'ASC')
                ->orderBy("sort", 'DESC')
                ->get();
        }

        $permissionArr = [];
        $menuArr = [];
        foreach ($roleMenuList as $roleMenu) {
            if ($roleMenu->type == 2) {//权限
                $permissionArr[] = $roleMenu->perms;
                continue;
            }
            $routeObj = [
                "path" => $roleMenu->path,
                "name" => $roleMenu->name,
                "component" => $roleMenu->component,
                "meta" => [
                    'title' => $roleMenu->title,
                    'icon' => $roleMenu->icon,
                ]
            ];
            if ($roleMenu->isHide) {
                $routeObj['meta']['hideMenu'] = true;
            }

            if ($roleMenu->type == 0) {//目录
                $roleMenu->pid > 0 ? $menuArr[$roleMenu->pid]['children'][$roleMenu->id] = $routeObj : $menuArr[$roleMenu->id] = $routeObj;
            } else {//菜单
                $pidArr = explode(",", $roleMenu->pids);
                if (count($pidArr) > 1) {
                    $menuArr[$pidArr[0]]['children'][$pidArr[1]]['children'][] = $routeObj;
                } else {
                    $menuArr[$pidArr[0]]['children'][] = $routeObj;
                }
            }
        }
        // dump($menuArr);
        //键值重置
        $j = 0;
        $menuNewArr = [];
        foreach ($menuArr as $menuOne) {
            $menuNewArr[$j] = $menuOne;
            $menuNewArr[$j]['children'] = [];
            $jj = 0;
            foreach ($menuOne['children'] as $menuTwo) {
                $menuNewArr[$j]['children'][$jj] = $menuTwo;
                $jjj = 0;
                if (empty($menuTwo['children'])) {
                    empty($menuNewArr[$j]['redirect']) && $menuNewArr[$j]['redirect'] = $menuOne['path'] . '/' . $menuTwo["path"];
                    count($menuOne['children']) <= 1 && $menuNewArr[$j]['meta']['hideChildrenInMenu'] = true;
                    continue;
                }
                $menuNewArr[$j]['children'][$jj]['children'] = [];
                unset($menuNewArr[$j]['children'][$jj]['component']);
                foreach ($menuTwo['children'] as $menuThree) {
                    empty($menuNewArr[$j]['redirect']) && $menuNewArr[$j]['redirect'] = $menuOne['path'] . '/' . $menuTwo["path"] . "/" . $menuThree['path'];
                    $menuNewArr[$j]['children'][$jj]['children'][$jjj] = $menuThree;
                    $jjj++;
                }
                $jj++;
            }
            $j++;
        }

        // dump($menuNewArr);
        $this->result['data']['permissionList'] = $permissionArr;
        $this->result['data']['menuList'] = $menuNewArr;
        return $this->result;
    }
}
