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
            "TM2594929823BR",
            "TM2595300841BR",
            "TM2594732645BR",
            "TM2595866341BR",
            "TM2590468211BR",
            "TM2592804906BR",
            "TM2592732338BR",
            "TM2590001146BR",
            "TM2593447622BR",
            "TM2595581132BR",
            "TM2595280948BR",
            "TM2595700950BR",
            "TM2594429635BR",
            "TM2595481021BR",
            "TM2562090704BR",
            "TM2594229458BR",
            "TM2590780820BR",
            "TM2592550151BR",
            "TM2594829727BR",
            "TM2594885739BR",
            "TM2593348556BR",
            "TM2593448604BR",
            "TM2591563346BR",
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
