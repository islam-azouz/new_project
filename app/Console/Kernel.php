<?php

namespace App\Console;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use App\Schedules\Inventory\AdjustmentsSchedule;
use App\Schedules\Invoicies\SendInvoicesToZatca;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Schedules\Notifications\DeleteOldNotificationsSchedule;

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


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
