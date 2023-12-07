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
        $this->scheduleDeletions($schedule);
        $this->scheduleOrders($schedule);
    }
    protected function scheduleDeletions(Schedule $schedule) {
        $schedule->command('app:del_job_monitors')->dailyAt('01:00');
        $schedule->command('app:del_bug_reports')->dailyAt('01:30');
        $schedule->command('app:del_sp_token_responses')->dailyAt('02:00');
    }

    protected function scheduleOrders(Schedule $schedule) {
        $schedule->call(function () {
            $this->delayDispatchJob(User::getActiveCallables(), 1800, function ($user, $delay) {
                dispatch(new \App\Jobs\AmazonOrders\CreateOrdersHistoryJob($user))->delay($delay);
            });
        })->everyThirtyMinutes();

        $schedule->call(function () {
            $this->delayDispatchJob(User::getActiveCallables(), 900, function (User $user, $delay) {
                dispatch(new \App\Jobs\AmazonOrders\GetLatestOrdersJob($user))->delay($delay);
            });
        })->everyFifteenMinutes();

        $schedule->call(function () {
            $this->delayDispatchJob(User::getActiveCallables(), 900, function ($user, $delay) {
                dispatch(new \App\Jobs\AmazonOrders\GetHistoricOrdersJob($user))->delay($delay);
            });
        })->cron('*/15 20-22 * * *');
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
