<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Reports;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Excel\Export\AnjunReport;
use App\Repositories\Reports\AnjunReportsRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
class ExportAnjunReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $user;
    public $reportRepository;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request, $user)
    {
        $this->user = $user;
        $this->request = $request;
        $this->reportRepository = new AnjunReportsRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $request = new Request($this->request);
            $deliveryBills = $this->reportRepository->getAnjunReport($request, $this->user);
            $id = $this->user->id;
            $exportService = new AnjunReport($deliveryBills, $id);
            $url = $exportService->handle();
            
            if ($url) {
                $report = Reports::find($request->report);
                if ($report) {
                    $report->update(['path' => $url, 'is_complete' => true]);
                    Log::error('path updated', ['path' => $url]);

                } else {
                    Log::error('Report not found', ['report_id' => $request->report]);
                }
            } else {
                Log::error('Export service did not return a URL', ['user_id' => $id]);
            }
        } catch (\Exception $e) {
            Log::error('An error occurred while processing the report', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $id,
                'request' => $request->all(),
            ]);
        }
        
    }
}
