<?php

namespace App\Console\Commands\Deletions;

use App\Models\JobMonitor;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class DeleteFromJobMonitor extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:del_job_monitors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is going to delete historic job_monitors';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @throws Exception
	 */
    public function handle() {
		$last_id = 0;

		do {
			console_log('Getting job_monitors gt ' . $last_id);
			$job_monitors = JobMonitor::query()->where('id', '>', $last_id)->take(1000)->cursor();
			$delete_ids = [];

			/** @var JobMonitor $job_monitor */
			foreach ($job_monitors as $job_monitor) {
				if (!$job_monitor->created_at->lt(Carbon::parse('15 days ago')->startOfDay())) {
					break;
				}

				$delete_ids[] = $job_monitor->id;
				$last_id = $job_monitor->id;
			}

			if (!empty($delete_ids)) {
				console_log('Deleting ' . count($delete_ids) . ' job_monitors');
				JobMonitor::query()->whereIn('id', $delete_ids)->delete();
			}

		} while (!empty($delete_ids));

    }

}
