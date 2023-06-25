<?php

namespace App\Jobs;

use App\Models\AdminModel;
use App\Utils\FunctionUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AdminOperateLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $requestArr;
    protected AdminModel $user;
    protected array $responseArr;
    protected string $remark;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AdminModel $user, $requestArr, $responseArr, $remark="")
    {
        $this->user = $user->withoutRelations();
        $this->requestArr = $requestArr;
        $this->responseArr = $responseArr;
        $this->remark = $remark;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //操作日志
        $insertData = [
            //请求和响应数据
            "url" => $this->requestArr["url"],
            "route" => $this->requestArr["path"],
            "params" => FunctionUtil::jsonEncode($this->requestArr["all"]),
            "response" => FunctionUtil::jsonEncode($this->responseArr),
            //客户端信息
            "user_id" => $this->user->id,
            "username" => $this->user->username,
            "ip" => $this->requestArr["ip"],
            "user_agent" => $this->requestArr["userAgent"],
            "remark" => $this->remark,
        ];
        DB::table('sys_operate_log')->insert($insertData);
    }
}
