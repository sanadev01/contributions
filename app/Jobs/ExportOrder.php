<?php

namespace App\Jobs;

use App\Models\Reports;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use App\Repositories\OrderRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Excel\Export\OrderExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request, OrderRepository $orderRepository)
    {
        $orders = $orderRepository->getOdersForExport($request);
        
        $exportService = new OrderExport($orders);
        $url = $exportService->handle();
        if($url) {
            $report = Reports::find($request->report);
            $report->update(['path'=> $url, 'is_complete' => true]);
        }
        
    }
}
