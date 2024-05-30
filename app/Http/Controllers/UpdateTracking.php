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
                ["tracking"=>"NC605559929BR","warehouse"=>"TM2594929823BR"],
                ["tracking"=>"NC605558835BR","warehouse"=>"TM2595300841BR"],
                ["tracking"=>"NC605560122BR","warehouse"=>"TM2594732645BR"],
                ["tracking"=>"NC605561573BR","warehouse"=>"TM2595866341BR"],
            ];
            return $this->updateTracking($codes, 4, 1);
       }
     function bCNToAnjunLabelsb() {  
                    $codes = [ 
                ["tracking"=>"NC620168531BR","warehouse"=>"TM2590468211BR"],
                ["tracking"=>"NC620166218BR","warehouse"=>"TM2592804906BR"],
                ["tracking"=>"NC605560096BR","warehouse"=>"TM2592732338BR"],
                ["tracking"=>"NC605558852BR","warehouse"=>"TM2590001146BR"],
            ];
            return $this->updateTracking($codes, 4, 1);
       }
     function bCNToAnjunLabelsc() {  
                    $codes = [ 
                ["tracking"=>"NC620167641BR","warehouse"=>"TM2593447622BR"],
                ["tracking"=>"NC620168995BR","warehouse"=>"TM2595581132BR"],
                ["tracking"=>"NC605562185BR","warehouse"=>"TM2595280948BR"],
                ["tracking"=>"NC605558849BR","warehouse"=>"TM2595700950BR"],
            ];
            return $this->updateTracking($codes, 4, 1);
       }
     function bCNToAnjunLabelsd() {  
                    $codes = [ 
                ["tracking"=>"NC620167071BR","warehouse"=>"TM2594429635BR"],
                ["tracking"=>"NC620168987BR","warehouse"=>"TM2595481021BR"],
                ["tracking"=>"NC546504374BR","warehouse"=>"TM2562090704BR"],
                ["tracking"=>"NC620167054BR","warehouse"=>"TM2594229458BR"],
            ];
            return $this->updateTracking($codes, 4, 1);
       }
     function bCNToAnjunLabelse() {  
                    $codes = [ 
                ["tracking"=>"NC620168973BR","warehouse"=>"TM2590780820BR"],
                ["tracking"=>"NC605560873BR","warehouse"=>"TM2592550151BR"],
                ["tracking"=>"NC605559915BR","warehouse"=>"TM2594829727BR"],
                ["tracking"=>"NC620169259BR","warehouse"=>"TM2594885739BR"],
            ];
            return $this->updateTracking($codes, 4, 1);
       }
     function bCNToAnjunLabelsf() {  
                    $codes = [ 
                ["tracking"=>"NC605560808BR","warehouse"=>"TM2593348556BR"],
                ["tracking"=>"NC620167709BR","warehouse"=>"TM2593448604BR"],
                ["tracking"=>"NC605561454BR","warehouse"=>"TM2591563346BR"],
        
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
