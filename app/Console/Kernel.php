<?php

namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\User;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\UpdateAnjunOrders::class,
        Commands\OrderArrivedCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('brazil:trackings')->everyMinute();
        $schedule->command('email:order-arrived')->dailyAt('22:00'); 
    }
    protected function delayDispatchJob($instances, $time, $callback) {
        if (!($total_inst = count($instances))) {
            return;
        }

        $count = 0;
        $delay_per_inst = floatval($time) / floatval($total_inst);
        foreach ($instances as $instance) {
            $callback($instance, floor(floatval($count) * floatval($delay_per_inst)));
            $count++;
        }
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
