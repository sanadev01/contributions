<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeleteLabel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:labels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command deletes label file from storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $orders = Order::where('corrios_tracking_code', '!=', null)
                        ->WhereBetween('order_date', [Carbon::now()->subMonths(6), Carbon::now()])
                        ->get();
        
        if ($orders) {
            Log::info('deleting labels...');

            foreach ($orders as $order) {
                if (Storage::disk('local')->exists('labels/'.$order->corrios_tracking_code.'.pdf')) {
                    Storage::disk('local')->delete('labels/'.$order->corrios_tracking_code.'.pdf');

                    Log::info('deleted label for order '.$order->corrios_tracking_code);
                }
            }              
        }
    }
}