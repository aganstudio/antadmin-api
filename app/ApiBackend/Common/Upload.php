<?php

namespace App\ApiBackend\Common;

use App\Http\Controllers\ApiBaseController;
use App\Jobs\AdminOperateLogJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 *  更新角色
 */
class Upload extends ApiBaseController
{

    /**
     * 参数检查
     */
    protected function check()
    {

        try {
            request()->validate([
                'file' => 'required',
            ]);
        } catch (\Exception $exception) {
            $this->result["code"] = 1;
            $this->result["message"] =  $exception->getMessage();
        }

    }

    /**
     * 业务主体
     */
    protected function service()
    {
        $file = request()->file('file');
        //检测
        $fileAllowType = [
            'png',
            'jpg',
            'mp4',
        ];
        if (!in_array($file->extension(), $fileAllowType)) {//类型限制
            $this->result['code'] = 1;
            $this->result['message'] = "文件 " . $file->extension() . " 格式不支持";
            return $this->result;
        }
        $fileAllowSize = 20 * 1024 * 1024;//20M
        if ($file->getSize() > $fileAllowSize) {//大小限制
            $this->result['code'] = 1;
            $this->result['message'] = "文件 " . (int)($file->getSize() / 1024 / 1024) . "M 超出20M限制";
            return $this->result;
        }
        // 主逻辑
        try {
            $path = $file->store('public/scm');
        } catch (\Exception $exception) {
            $this->result['code'] = 1;
            $this->result['message'] = $exception->getMessage();
            return $this->result;
        }
        $domain = config('app.url');
        $this->result['url'] = $domain . "/" . str_replace('public/', 'storage/', $path);

        return $this->result;
    }
}
