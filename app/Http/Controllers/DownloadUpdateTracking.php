<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Models\Order;
use App\Repositories\Calculator\CalculatorRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Services\Excel\Export\OrderUpdateExport;
use Exception;

class DownloadUpdateTracking extends Controller
{
    function bCNToAnjunLabels()
    {
        $codes = [
            'TM2602107802BR',
            'TM2602007736BR',
            'TM2601907550BR',
            'TM2603905800BR',
            'TM2604106207BR',
            'TM2602007617BR',
            'TM2603905913BR',
            'TM2603805750BR',
            'TM2603805638BR',
            'HD2282155927BR',
        ];
        return $this->updateTracking($codes);
    }

    function updateTracking($codes)
    {
        set_time_limit(1500);
        $ordersDetails = [];

        try {
            $orders = Order::whereIn('warehouse_number', $codes)->get();
            
            $ordersMap = [];
            foreach ($orders as $order) {
                $ordersMap[$order->warehouse_number] = $order;
            }

            foreach ($codes as $code) {
                if (!isset($ordersMap[$code])) {
                    $ordersDetails[] = [
                        'tracking_old' => $code,
                        'warehouse' => $code,
                        'tracking_new' => 'not found',
                        'link' => 'not found',
                        'poboxName' => 'not found',
                    ];
                } else {
                    $order = $ordersMap[$code];
                    $ordersDetails[] = [
                        'tracking_old' => $code,
                        'warehouse' => $order->warehouse_number,
                        'tracking_new' => $order->corrios_tracking_code,
                        'link' => route('order.label.download', encrypt($order->id)),
                        'poboxName' => $order->user->pobox_name,
                    ];  
                }
            }
        } catch (Exception $e) {
            \Log::error('Error processing orders: ' . $e->getMessage());
        }

        if (!empty($ordersDetails)) {
            $exports = new OrderUpdateExport($ordersDetails);
            return response()->download($exports->handle());
        } else {
            echo 'order not found';
            dd($codes);
        }
    }


}
