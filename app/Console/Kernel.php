<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Module\Wms\Cron\OrderFinishNoticeCron;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // 发货通知
        $schedule->call(new OrderFinishNoticeCron())->everyFiveMinutes()->between('7:00', '19:00');

        //监控
        $schedule->command('telescope:prune --hours=96')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('app/Routes/console.php');
    }
}
