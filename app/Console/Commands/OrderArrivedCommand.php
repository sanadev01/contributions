<?php

namespace App\Console\Commands;

use App\Mail\User\Shipment;
use App\Models\Order;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderArrivedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:order-arrived';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order arrived notification';

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
        $order = Order::find(4024); 
        $date = (new DateTime('America/New_York'))->format('Y-m-d h:i:s');
        $order->update([
            'arrived_date' => $date
        ]);

        try{
            Mail::send(new Shipment($order));
        }catch(Exception $e){
            Log::info($e->getMessage());
        }      
        return 0;
    }
}
