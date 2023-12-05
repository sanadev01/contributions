<?php

namespace App\Console\Commands;

use App\Jobs\BaseJob;
use App\Models\User;
use Illuminate\Console\Command;

class ExecuteJob extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:job {job_class} {--user=1} {--job_type=orders} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command helps executing jobs manually';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $user = $this->option('user');
        if (!$user || $user == 'all') {
            $users = $this->getCallableUsers();
            foreach ($users as $user) {
                $this->executeJob($user);
            }
        } else {
            $user_ids = explode(',', $this->option('user'));
            foreach ($user_ids as $user_id) {
                /** @var User $user */
                $user = User::query()->find($user_id);
                $this->executeJob($user);
            }
        }
    }

    protected function executeJob($user) {
        $jobClass = $this->getDirectory() . $this->argument('job_class');

        /** @var BaseJob $job */
        $job = new $jobClass($user);
        if ($this->option('force')) {
            $job->setJobDone();
        }

        $job->handle();

        console_log('Process (for USER ' . $user->id . ') finished');
    }

    protected function getDirectory(): string {
        $directory = 'App\\Jobs\\';

        $jobType = $this->option('job_type');

        switch ($jobType) {
            case 'orders':
                return $directory . 'AmazonOrders\\';
            default:
                return $directory;
        }

    }

    protected function getCallableUsers()
    {
        $jobType = $this->option('job_type');

        switch ($jobType) {
            case 'orders':
                return User::getActiveCallables([User::USER_TYPE_SELLER]);
            default:
                return User::all();
        }
    }
}
