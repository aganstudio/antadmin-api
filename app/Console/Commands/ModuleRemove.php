<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ModuleRemove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:remove {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'module remove';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
