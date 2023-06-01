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
        $date = (new DateTime('America/New_York'))->format('Y-m-d');
        $users = Order::where('arrived_date', '>=',$date.' 00:00:00')->where('arrived_date', '<=',$date.' 23:59:59')
                ->get()
                ->groupBy('user_id');
                foreach($users as  $userOrder){
                    try{
                        Mail::send(new Shipment($userOrder));
                    }catch(Exception $e){
                       echo $e->getMessage();
                        Log::info(['order arrived error:',$e->getMessage()]);
                    }
                }

        return 0;

    }
}
