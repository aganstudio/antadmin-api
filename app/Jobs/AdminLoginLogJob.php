<?php

namespace App\Jobs;

use App\Models\AdminModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AdminLoginLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private AdminModel $user;
    private object $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $request)
    {
        $this->user = $user->withoutRelations();
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //更新登录ip
        DB::table('personal_access_tokens')->where("tokenable_id", $this->user->id)->update(["ip" => $this->request->ip]);
        //登录日志
        $insertData = [
            "user_id" => $this->user->id,
            "username" => $this->user->username,
            "ip" => $this->request->ip,
            "user_agent" => $this->request->userAgent,
        ];
        DB::table('admin_login_log')->insert($insertData);
    }
}
