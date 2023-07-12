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
        $orders = $this->orderRepository->getOdersForExport(optional($this->request), $this->user);
        $id = $this->user->id;
        if(optional($this->request)['type'] == 'anjun'){
            $exportService = new AnjunReport($orders, $id);
        }else{
            $exportService = new OrderExport($orders, $id);
        }
        $url = $exportService->handle();

        if($url) {
            $report = Reports::find(optional($this->request)['report']);
            $report->update(['path'=> $url, 'is_complete' => true]);
        }
        
    }
}
