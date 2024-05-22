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
    
    function bCNToAnjunLabelsBatch1() {
        $codes = [
            ['tracking'=>'IX030958569BR','warehouse'=>'HD2602501012BR'],
            ['tracking'=>'NC574417733BR','warehouse'=>'TM2583366004BR'],
            ['tracking'=>'NC575449600BR','warehouse'=>'TM2583165744BR'],
            ['tracking'=>'NC605558631BR','warehouse'=>'TM2584197540BR'],
            ['tracking'=>'NC605558702BR','warehouse'=>'TM2583996536BR'],
            ['tracking'=>'NC605559036BR','warehouse'=>'TM2592204534BR'],

         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsBatch2() {
        $codes = [
            
            ['tracking'=>'NC605560723BR','warehouse'=>'TM2593047000BR'],
            ['tracking'=>'NC605561468BR','warehouse'=>'TM2591663410BR'],
            ['tracking'=>'NC605562313BR','warehouse'=>'TM2591083910BR'],
            ['tracking'=>'NC605562392BR','warehouse'=>'TM2594885954BR'],
            ['tracking'=>'NC620165861BR','warehouse'=>'TM2584197653BR'],
            ['tracking'=>'NC620165889BR','warehouse'=>'TM2584297820BR'],
            
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsBatch3() {
        $codes = [
            ['tracking'=>'NC620169276BR','warehouse'=>'TM2594986247BR'],
            ['tracking'=>'NC620169293BR','warehouse'=>'TM2595286832BR'],
            ['tracking'=>'NC620169815BR','warehouse'=>'TM2602701443BR'],
            ['tracking'=>'NC653642348BR','warehouse'=>'TM2590087925BR'],
            ['tracking'=>'NC653642949BR','warehouse'=>'TM2604403951BR'],

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
                            ->update(['shipping_service_id' => 42])
                        ) {
                            $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                            $corrieosBrazilLabelRepository->run($order, true);
                        }
                        if (Order::where('warehouse_number', $code['warehouse'])
                            ->where('corrios_tracking_code', 'like', 'NC%')
                            ->update(['shipping_service_id' => 16])
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
