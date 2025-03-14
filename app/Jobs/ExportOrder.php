<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Reports;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use App\Repositories\OrderRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Excel\Export\AnjunReport;
use App\Services\Excel\Export\OrderExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $user;
    public $orderRepository;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $user)
    {
        $this->user = $user;
        $this->request = $request;
        $this->orderRepository = new OrderRepository();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $request = new Request($this->request);
        $orders = $this->orderRepository->getOrdersForExport($request, $this->user);
        $id = $this->user->id;
        $exportService = new OrderExport($orders, $id);
        $url = $exportService->handle();

        if($url) {
            $report = Reports::find($request->report);
            $report->update(['path'=> $url, 'is_complete' => true]);
        }
        
    }
}
