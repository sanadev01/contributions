<?php

namespace App\Console\Commands\Deletions;

use App\Models\Connections\SpTokenResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class DeleteFromSpTokenResponse extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:del_sp_token_responses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is going to delete historic sp_token_responses';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @throws Exception
	 */
    public function handle() {
		$last_id = 0;

		do {
			console_log('Getting tk_responses gt ' . $last_id);
			$tk_responses = SpTokenResponse::query()->where('id', '>', $last_id)->take(1000)->cursor();
			$delete_ids = [];

			/** @var SpTokenResponse $tk_response */
			foreach ($tk_responses as $tk_response) {
				if (!$tk_response->created_at->lt(Carbon::parse('30 days ago')->startOfDay())) {
					break;
				}

				$delete_ids[] = $tk_response->id;
				$last_id = $tk_response->id;
			}

			if (!empty($delete_ids)) {
				console_log('Deleting ' . count($delete_ids) . ' tk_responses');
				SpTokenResponse::query()->whereIn('id', $delete_ids)->delete();
			}

		} while (!empty($delete_ids));

    }

}
