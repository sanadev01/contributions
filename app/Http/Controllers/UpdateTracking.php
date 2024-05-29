<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calculator\CalculatorRequest;
use App\Models\Order;
use App\Repositories\Calculator\CalculatorRepository;
use App\Repositories\CorrieosBrazilLabelRepository;
use App\Services\Excel\Export\OrderUpdateExport;
use Exception;

class UpdateTracking extends Controller
{
    function bCNToAnjunLabelsa() {
        $codes = [
            ['tracking'=>'NC605562083BR','warehouse'=>'TM2594379025BR'],
            ['tracking'=>'IX030958475BR','warehouse'=>'TM2591778926BR'],
            ['tracking'=>'IX030958484BR','warehouse'=>'TM2595579229BR'],
            ['tracking'=>'NC605562106BR','warehouse'=>'TM2591979658BR'],
            ['tracking'=>'IX030886354BR','warehouse'=>'TM2591679517BR'],
            ['tracking'=>'NC605562110BR','warehouse'=>'TM2593479735BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsb() {
        $codes = [
            ['tracking'=>'NC620168925BR','warehouse'=>'TM2595079125BR'],
            ['tracking'=>'NC605562097BR','warehouse'=>'TM2591179452BR'],
            ['tracking'=>'NC605558628BR','warehouse'=>'TM2583996643BR'],
            ['tracking'=>'NC550899276BR','warehouse'=>'TM2570848722BR'],
            ['tracking'=>'NC620165725BR','warehouse'=>'HD2580494652BR'],
            ['tracking'=>'NC620165892BR','warehouse'=>'TM2583896009BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsc() {
        $codes = [
            ['tracking'=>'NC605558614BR','warehouse'=>'TM2583795852BR'],
            ['tracking'=>'NC605558659BR','warehouse'=>'TM2583896253BR'],
            ['tracking'=>'NC605558662BR','warehouse'=>'TM2580695438BR'],
            ['tracking'=>'NC605558676BR','warehouse'=>'TM2583996304BR'],
            ['tracking'=>'NC695740346BR','warehouse'=>'HD2282155927BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function updateTracking($codes, $batchSize = 4, $delay = 1)
    {
        set_time_limit(400);

        $chunks = array_chunk($codes, $batchSize);

        try {
            foreach ($chunks as $chunk) {
                foreach ($chunk as $code) {
                    $order = Order::where('warehouse_number',  $code['warehouse'])->first();

                    if ($order && $code['tracking'] == $order->corrios_tracking_code) {
                        if (Order::where('warehouse_number', $code['warehouse'])
                            ->where('corrios_tracking_code', 'like', 'IX%')
                            ->update(['shipping_service_id' => 3])
                        ) {
                            $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                            $corrieosBrazilLabelRepository->run($order, true);
                        }
                        if (Order::where('warehouse_number', $code['warehouse'])
                            ->where('corrios_tracking_code', 'like', 'NC%')
                            ->update(['shipping_service_id' => 1])
                        ) {
                            $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                            $corrieosBrazilLabelRepository->run($order, true);
                        }
                        if (Order::where('warehouse_number', $code['warehouse'])
                            ->where('corrios_tracking_code', 'like', 'NB%')
                            ->update(['shipping_service_id' => 1])
                        ) {
                            $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                            $corrieosBrazilLabelRepository->run($order, true);
                        }
                    }
                }

                sleep($delay);
            }

            dd('done');
        } catch (Exception $e) {
            dd(['error' => $e->getMessage()]);
        }
    }


    // function updateTracking($codes)
    // {
    //     set_time_limit(400);
    //     //update standard 
    //     try {
    //         foreach ($codes as $code) {

    //             $order = Order::where('warehouse_number',  $code['warehouse'])->first();

    //             if ($code['tracking'] == $order->corrios_tracking_code) {

    //                 if (Order::where('warehouse_number', $code['warehouse'])
    //                     ->where('corrios_tracking_code', 'like', 'IX%')
    //                     ->update(['shipping_service_id' => 42])
    //                 ) {
    //                     $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
    //                     $corrieosBrazilLabelRepository->run($order, true);
    //                 }
    //                 if (Order::where('warehouse_number', $code['warehouse'])
    //                     ->where('corrios_tracking_code', 'like', 'NC%')
    //                     ->update(['shipping_service_id' => 16])
    //                 ) {
    //                     $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
    //                     $corrieosBrazilLabelRepository->run($order, true);
    //                 }
    //             }
    //         }
    //         dd('done');
    //     } catch (Exception $e) {
    //         dd(['error' => $e->getMessage()]);
    //     }
    // }
}
