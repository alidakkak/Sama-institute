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
        $schedule->command('app:sync-changes-to-server')->everyFiveMinutes();
        $schedule->command('app:image-uploader')->everyFiveMinutes();
        $schedule->command('app:retry-failed-notifications')->everyFiveMinutes();
        $schedule->command('app:fetch-attendance')->everyFiveMinutes();
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
