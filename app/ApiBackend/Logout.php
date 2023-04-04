<?php

namespace App\ApiBackend;

use App\Http\Controllers\ApiBaseController;
use Illuminate\Support\Facades\DB;

/**
 *  退出
 */
class Logout extends ApiBaseController
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
        $token = request()->bearerToken();
        $tokenArr = explode("|", $token);
        $tokenId = (int)$tokenArr[0];
        if (!$token && $tokenId > 0) {
            return ['code' => 1, 'message' => '未登录'];
        } else {
            DB::table("personal_access_tokens")->where('id', $tokenId)->delete();
            return ['code' => 0, 'message' => '退出登录成功'];
        }
    }
}
