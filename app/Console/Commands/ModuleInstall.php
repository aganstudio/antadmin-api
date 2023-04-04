<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:install {name} --force';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'module install';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //模块名
        $name = $this->argument('name');
        $force = $this->option('force');
        //检测是否安装过
        $flag = true;
        if(File::exists(base_path(config('module.path').'/'.$name)))
        {
            if (!$this->confirm('You have already installed, Do you wish to continue?')) {
                $flag = false;
            }
        }
        // todo 下载文件
        return Command::SUCCESS;
    }
}
