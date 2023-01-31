<?php

namespace App\Jobs;

use App\Models\User;
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
        $orders = $this->orderRepository->getOdersForExport($request, $this->user);
        // dd($this->user->id);
        $id = $this->user->id;
        $exportService = new OrderExport($orders, $id);
        $url = $exportService->handle();
        if($url) {
            $id = Reports::orderBy('id', 'desc')->value('id');
            $report = Reports::find($id);
            $report->update(['path'=> $url, 'is_complete' => true]);
        }
        
    }
}
