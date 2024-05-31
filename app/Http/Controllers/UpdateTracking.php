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
                ["tracking"=>"NC253044216BR","warehouse"=>"TM2604706545BR"],
                ["tracking"=>"NC560917853BR","warehouse"=>"TM2605206945BR"],
                ["tracking"=>"NC253044255BR","warehouse"=>"TM2601407328BR"],
                ["tracking"=>"NC560917875BR","warehouse"=>"TM2601207139BR"],
            ];
            return $this->updateTracking($codes, 4, 1);
       }
     function bCNToAnjunLabelsb() {  
                    $codes = [ 
                ["tracking"=>"NC560917840BR","warehouse"=>"TM2604906746BR"],
                ["tracking"=>"NC253044247BR","warehouse"=>"TM2601307247BR"],
                ["tracking"=>"NC253044233BR","warehouse"=>"TM2605106847BR"],
                ["tracking"=>"NC574416517BR","warehouse"=>"TM2581237648BR"],
            ];
            return $this->updateTracking($codes, 4, 1);
       }
     function bCNToAnjunLabelsc() {  
                    $codes = [ 
                ["tracking"=>"IX031016495BR","warehouse"=>"TM2610331052BR"],
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
