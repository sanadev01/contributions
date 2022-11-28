<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Order;
use GuzzleHttp\Client;
use App\Models\Setting;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusChanged implements ShouldQueue
{
    use InteractsWithQueue;

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
     * @param  OrderStatusUpdated  $event
     * @return void
     */
    public function handle(OrderStatusUpdated $orderstatusupdated)
    {
        $orders = json_decode( json_encode($orderstatusupdated), true);

        if(isset($orders['orders']['user_id'])){
            $user = User::where('id', $orders['orders']['user_id'])->get();
        }else {
            $user = User::where('id', $orders['orders'][0]['user_id'])->get();  
        }
        if(isset($orders['orders']['status'])){
            $statusCode = $orders['orders']['status'];
        }else {
            $statusCode = $orders['orders'][0]['status'];
        }
        if(isset($orders['orders']['id'])){
            $orderId = $orders['orders']['id'];
        }else {
            $orderId = $orders['orders'][0]['id'];
        }
        \Log::info($statusCode);
        \Log::info($orderId);
        \Log::info(getParcelStatus($statusCode));
        //$url = Setting::where('id', $orders['orders']['user_id'])->value('url');
        $url = 'http://localhost/webhook?65165=lkjkl';

        $client = new Client([]);
        try {

            $response = $client->post($url,[
                'json' => [
                    'data' => [
                        'warehouse_number' => $orderId,
                        'status' => "Your Parcel Status Code is ".''. $statusCode,
                        'message' => "Your Parcel Status is ".''. getParcelStatus($statusCode),
                        'format' => 'json',
                    ]
                ]
            ]);

        } catch (\Exception $th) {
            abort(400,'Bad Request'.$th->getMessage());
        }
        return json_decode($response->getBody()->getContents());
    }
}
