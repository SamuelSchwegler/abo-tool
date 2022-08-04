<?php

namespace App\Console;

use App\Jobs\CancelUnaffordableOrders;
use App\Jobs\CreateDeliveries;
use App\Jobs\DeliveryOrderReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        $schedule->job(new CancelUnaffordableOrders)->dailyAt('02:05');
        $schedule->job(new CreateDeliveries)->dailyAt('02:10');
        $schedule->job(new DeliveryOrderReminder)->dailyAt('04:30');

        // db backups
        $schedule->command('db:backup')->daily()->everyFourHours()->between('6:00', '22:00');
        $schedule->command('db:backup:clean')->dailyAt('04:10');
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
