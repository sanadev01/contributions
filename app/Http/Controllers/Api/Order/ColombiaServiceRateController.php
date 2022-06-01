<?php

namespace App\Http\Controllers\Api\Order;

use App\Facades\ColombiaShippingFacade;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ColombiaServiceRateController extends Controller
{
    protected $currentUSDollar = 0.000254;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $order = Order::find($request->order_id);

        $response = ColombiaShippingFacade::getServiceRates($order);

        if ($response['success'] == true) {
            return [
                'success' => true,
                'total_amount' => number_format(($response['data']['decTotalRate'] * $this->currentUSDollar), 2),
            ];
        }

        return [
            'success' => false,
            'total_amount' => 0,
            'error' => $response['error'],
        ];
    }
}
