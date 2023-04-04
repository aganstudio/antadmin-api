<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class AdminModel extends \Illuminate\Foundation\Auth\User
{
    //使用HasApiTokens
    use HasFactory, HasApiTokens;

    public $table = 'admin';

    /**
     * 获取 sanctum token
     * @return string
     */
    public function getAuthToken(): string
    {
        // 匹配 user-agent
        $pattern = '/^(\S*)\s(\([\w\s\.\;]*\))\s([\w\/\.]*)\s(\([\w\s\,]*\))\s([\w\/\.]*)\s([\w\/\.]*)/i';;
        preg_match($pattern, request()->header("user-agent"), $matchArr);
        if (empty($matchArr) || count ($matchArr) <6 )
        {
            $tokenName = "user-agent";
        }
        else
        {
            $tokenName = str_replace(';', "", $matchArr[2]) . " " . $matchArr[5];
        }
        //创建token
        $expireDate = new \DateTime();
        $expireDate->setTimestamp(strtotime("+1 days"));
        $token = $this->createToken($tokenName, ['*'], $expireDate);

        return $token->plainTextToken;
    }

    /**
     * 删除当前用户 sanctum token
     * @return mixed
     */
    public function destroyAuthTokens()
    {
        return $this->tokens()->delete();
    }

}
