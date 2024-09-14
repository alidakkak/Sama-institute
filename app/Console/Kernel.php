<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:sync-changes-to-server')->everyMinute();
        $schedule->command('app:image-uploader')->everyMinute();
        $schedule->command('app:retry-failed-notifications')->everyMinute();
        $schedule->command('app:fetch-attendance')->everyMinute();
        //        $schedule->call(function () {
        //            app('App\Http\Controllers\AttendanceController')->fetchAttendance();
        //        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
