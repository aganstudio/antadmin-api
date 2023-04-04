<?php

namespace App\ApiBackend;

use App\Http\Controllers\ApiBaseController;
use App\Jobs\AdminLoginLogJob;
use App\Models\AdminModel;
use App\Models\SysAdmin;
use Illuminate\Support\Facades\Hash;

/**
 *  登陆
 */
class Login extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {
        try {
            request()->validate([
                'username' => 'required',
                'password' => 'required',
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
        $user = AdminModel::where('username', \request('username'))->select([
            "id",
            "name",
            "username",
            "password",
            "status",
        ])->first();
        if (!$user) {
            $this->result["code"] = 1;
            $this->result["message"] = "登陆失败: 账号不存在 ";
            return $this->result;
        }
        if ($user->status === 0) {
            $this->result["code"] = 1;
            $this->result["message"] = "登陆失败: 账号已禁用 ";
            return $this->result;
        }
        if (!Hash::check(\request('password'), $user->password)) {
            $this->result["code"] = 1;
            $this->result["message"] = "登陆失败: 用户密码错误 ";
            return $this->result;
        }
        //删除之前token
        $user->destroyAuthTokens();
        $this->result['data']['token'] = $user->getAuthToken();

        //登录日志
        $requestLog = [
            "ip"=>request()->ip(),
            "userAgent"=>request()->header("user-agent"),
        ];
        AdminLoginLogJob::dispatch($user,(Object)$requestLog);//->onQueue('log');
        return $this->result;
    }
}
