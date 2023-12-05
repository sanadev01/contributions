<?php

namespace App\Console\Commands\Deletions;

use App\Models\BugReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class DeleteFromBugReports extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:del_bug_reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is going to delete historic bug_reports';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @throws Exception
	 */
    public function handle() {
		$last_id = 0;

		do {
			console_log('Getting bug_reports gt ' . $last_id);
			$bug_reports = BugReport::query()->where('id', '>', $last_id)->take(1000)->cursor();
			$delete_ids = [];

			/** @var BugReport $bug_report */
			foreach ($bug_reports as $bug_report) {
				if (!$bug_report->created_at->lt(Carbon::parse('15 days ago')->startOfDay())) {
					break;
				}

				$delete_ids[] = $bug_report->id;
				$last_id = $bug_report->id;
			}

			if (!empty($delete_ids)) {
				console_log('Deleting ' . count($delete_ids) . ' bug_reports');
				BugReport::query()->whereIn('id', $delete_ids)->delete();
			}

		} while (!empty($delete_ids));

    }

}
