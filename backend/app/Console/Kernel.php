<?php

namespace App\Console;

use App\Console\Commands\NotifyTasks;
use App\Console\Commands\RegularPaymentNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('newsletters:send')->everyMinute();
        $schedule->command(RegularPaymentNotification::class)->everyMinute();

        $settings = DB::table("settings")->first();
        if($settings && $settings->notification_time){
            $schedule->command(NotifyTasks::class)->dailyAt($settings->notification_time);
        }

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
