<?php

namespace App\Listeners;

use App\Events\OrderReport;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportOrder
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(OrderReport $orderReport)
    {
        \Log::info('event logs');
        \Log::info($orderReport->request->toArray());
    }
}
