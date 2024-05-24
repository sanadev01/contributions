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
            ['tracking'=>'NC620168298BR','warehouse'=>'TM2591463055BR'],
            ['tracking'=>'NC605562035BR','warehouse'=>'TM2595377202BR'],
            ['tracking'=>'NC620168885BR','warehouse'=>'TM2595477451BR'],
            ['tracking'=>'NC605562168BR','warehouse'=>'TM2591180600BR'],
            ['tracking'=>'NC620168942BR','warehouse'=>'TM2593480014BR'],
            ['tracking'=>'NC620168908BR','warehouse'=>'TM2595577514BR'],
            
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsb() {
        $codes = [
            ['tracking'=>'NC620168911BR','warehouse'=>'TM2593878840BR'],
            ['tracking'=>'NC653642586BR','warehouse'=>'TM2590595635BR'],
            ['tracking'=>'NC550899695BR','warehouse'=>'TM2573960803BR'],
            ['tracking'=>'NC605559583BR','warehouse'=>'TM2593422148BR'],
            ['tracking'=>'NC620168015BR','warehouse'=>'TM2593857148BR'],
            ['tracking'=>'NC620168823BR','warehouse'=>'TM2594476151BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsc() {
        $codes = [
            ['tracking'=>'NC605562123BR','warehouse'=>'TM2594779905BR'],
            ['tracking'=>'NC605561220BR','warehouse'=>'TM2593857004BR'],
            ['tracking'=>'NC620168899BR','warehouse'=>'TM2595577625BR'],
            ['tracking'=>'NC560917796BR','warehouse'=>'TM2603906056BR'],
            ['tracking'=>'NC560917751BR','warehouse'=>'TM2600305533BR'],
            ['tracking'=>'NC560917725BR','warehouse'=>'TM2602704942BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsd() {
        $codes = [
            ['tracking'=>'NC560917805BR','warehouse'=>'TM2604006126BR'],
            ['tracking'=>'NC253044318BR','warehouse'=>'TM2602107945BR'],
            ['tracking'=>'NC253044193BR','warehouse'=>'TM2603805154BR'],
            ['tracking'=>'NC253047518BR','warehouse'=>'TM2615002205BR'],
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
