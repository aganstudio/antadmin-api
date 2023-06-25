<?php

namespace App\Http\Controllers\Monitor\Online;

use App\Http\Controllers\ApiBaseController;
use App\Models\SysAdmin;

/**
 *  更新用户信息
 */
class OnlineKick extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {
        try {
            request()->validate([
                'uid' => 'required',
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
        //数据
        $user = SysAdmin::where('id', \request('uid'))->select([
            "id",
            "name",
        ])->first();
        if (!$user) {
            $this->result["code"] = 1;
            $this->result["message"] = "账号不存在 ";
            return $this->result;
        }
        //删除token
        $user->destroyAuthTokens();
        $this->result['data']['message'] = "用户下线成功";
        return $this->result;
    }
}
