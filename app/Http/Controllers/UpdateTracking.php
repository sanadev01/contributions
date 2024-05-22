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
    // Zone1
    function bCNToAnjunLabelsZone1a() {
        $codes = [
            ['tracking'=>'NC550899599BR','warehouse'=>'HD2570056645BR'],
            ['tracking'=>'NC605558716BR','warehouse'=>'TM2584197315BR'],
            ['tracking'=>'NC620165858BR','warehouse'=>'TM2584197428BR'],
            ['tracking'=>'NC620166495BR','warehouse'=>'TM2591614607BR'],
            ['tracking'=>'NC620167085BR','warehouse'=>'HD2595530221BR'],
            ['tracking'=>'NC605561255BR','warehouse'=>'TM2590457508BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1b() {
        $codes = [
            ['tracking'=>'NC605561525BR','warehouse'=>'TM2592665526BR'],
            ['tracking'=>'NC605561825BR','warehouse'=>'TM2594472450BR'],
            ['tracking'=>'NC620168939BR','warehouse'=>'TM2593579816BR'],
            ['tracking'=>'NC620169642BR','warehouse'=>'TM2592096917BR'],
            ['tracking'=>'NC620169917BR','warehouse'=>'TM2605604552BR'],
            ['tracking'=>'NC620168514BR','warehouse'=>'TM2593767207BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1c() {
        $codes = [
            ['tracking'=>'NC620169205BR','warehouse'=>'TM2594484654BR'],
            ['tracking'=>'NC620169656BR','warehouse'=>'TM2592797102BR'],
            ['tracking'=>'NC653642674BR','warehouse'=>'TM2592797218BR'],
            ['tracking'=>'NC620169660BR','warehouse'=>'TM2592797348BR'],
            ['tracking'=>'NC605559478BR','warehouse'=>'TM2593320355BR'],
            ['tracking'=>'NC605559481BR','warehouse'=>'TM2593420403BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1d() {
        $codes = [
            ['tracking'=>'NC605559495BR','warehouse'=>'TM2593420510BR'],
            ['tracking'=>'NC605559623BR','warehouse'=>'TM2593822616BR'],
            ['tracking'=>'NC605559637BR','warehouse'=>'TM2593922700BR'],
            ['tracking'=>'NC620166782BR','warehouse'=>'TM2594723610BR'],
            ['tracking'=>'NC605559685BR','warehouse'=>'TM2594823821BR'],
            ['tracking'=>'NC605559699BR','warehouse'=>'TM2594823938BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1e() {
        $codes = [
            ['tracking'=>'NC605560255BR','warehouse'=>'TM2594335538BR'],
            ['tracking'=>'NC605560459BR','warehouse'=>'TM2590538819BR'],
            ['tracking'=>'NC605560462BR','warehouse'=>'TM2590538933BR'],
            ['tracking'=>'NC605560476BR','warehouse'=>'TM2590539043BR'],
            ['tracking'=>'NC620167350BR','warehouse'=>'TM2591840113BR'],
            ['tracking'=>'NC620167385BR','warehouse'=>'TM2593340605BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1f() {
        $codes = [
            ['tracking'=>'NC605560581BR','warehouse'=>'TM2591442419BR'],
            ['tracking'=>'NC605560745BR','warehouse'=>'TM2593147209BR'],
            ['tracking'=>'NC605560754BR','warehouse'=>'TM2593147334BR'],
            ['tracking'=>'NC605560768BR','warehouse'=>'TM2593147449BR'],
            ['tracking'=>'NC620167805BR','warehouse'=>'TM2591151459BR'],
            ['tracking'=>'NC605560944BR','warehouse'=>'TM2591251514BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1g() {
        $codes = [
            ['tracking'=>'NC605562225BR','warehouse'=>'TM2590682202BR'],
            ['tracking'=>'NC605562273BR','warehouse'=>'TM2590782937BR'],
            ['tracking'=>'NC620169090BR','warehouse'=>'TM2590783044BR'],
            ['tracking'=>'NC605562295BR','warehouse'=>'TM2590983328BR'],
            ['tracking'=>'NC620169165BR','warehouse'=>'TM2591084158BR'],
            ['tracking'=>'NC620169174BR','warehouse'=>'TM2594384205BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1h() {
        $codes = [
            ['tracking'=>'NC620169188BR','warehouse'=>'TM2594384334BR'],
            ['tracking'=>'NC620169214BR','warehouse'=>'TM2594584911BR'],
            ['tracking'=>'NC620169228BR','warehouse'=>'TM2594685010BR'],
            ['tracking'=>'NC605562389BR','warehouse'=>'TM2594885606BR'],
            ['tracking'=>'NC653642285BR','warehouse'=>'TM2594986006BR'],
            ['tracking'=>'NC653642294BR','warehouse'=>'TM2594986127BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1i() {
        $codes = [
            ['tracking'=>'NC620169302BR','warehouse'=>'TM2595386913BR'],
            ['tracking'=>'NC620169316BR','warehouse'=>'TM2595387029BR'],
            ['tracking'=>'NC620169320BR','warehouse'=>'TM2595487131BR'],
            ['tracking'=>'NC620169333BR','warehouse'=>'TM2595487252BR'],
            ['tracking'=>'NC620169355BR','warehouse'=>'TM2595987412BR'],
            ['tracking'=>'NC620169381BR','warehouse'=>'TM2590087707BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1j() {
        $codes = [
            ['tracking'=>'NC653642538BR','warehouse'=>'TM2594294918BR'],
            ['tracking'=>'NC653642541BR','warehouse'=>'TM2594295022BR'],
            ['tracking'=>'NC620169608BR','warehouse'=>'TM2594295127BR'],
            ['tracking'=>'NC620169611BR','warehouse'=>'TM2594295231BR'],
            ['tracking'=>'IX030886385BR','warehouse'=>'TM2593796209BR'],
            ['tracking'=>'NC620169832BR','warehouse'=>'TM2603301848BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone1k() {
        $codes = [
            ['tracking'=>'NC653642966BR','warehouse'=>'TM2605504217BR'],
            ['tracking'=>'NC653642555BR','warehouse'=>'TM2595195353BR'],
            ['tracking'=>'NC653642626BR','warehouse'=>'TM2595696415BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }
    // Zone 2
    function bCNToAnjunLabelsZone2a() {
        $codes = [
            ['tracking'=>'NC522425181BR','warehouse'=>'TM2562644135BR'],
            ['tracking'=>'NC620165901BR','warehouse'=>'TM2580695326BR'],
            ['tracking'=>'NC605558645BR','warehouse'=>'TM2583996756BR'],
            ['tracking'=>'NC620165827BR','warehouse'=>'TM2584096810BR'],
            ['tracking'=>'NC620165929BR','warehouse'=>'TM2584096922BR'],
            ['tracking'=>'NC620165875BR','warehouse'=>'TM2584097036BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2b() {
        $codes = [
            ['tracking'=>'NC605558680BR','warehouse'=>'TM2584097149BR'],
            ['tracking'=>'NC605559305BR','warehouse'=>'TM2591614855BR'],
            ['tracking'=>'NC605559314BR','warehouse'=>'TM2591714918BR'],
            ['tracking'=>'NC605560414BR','warehouse'=>'TM2595538108BR'],
            ['tracking'=>'NC605560431BR','warehouse'=>'TM2595638423BR'],
            ['tracking'=>'NC620168324BR','warehouse'=>'TM2591663634BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2c() {
        $codes = [
            ['tracking'=>'NC620168426BR','warehouse'=>'TM2590365705BR'],
            ['tracking'=>'NC605561746BR','warehouse'=>'TM2593570435BR'],
            ['tracking'=>'NC605561750BR','warehouse'=>'TM2593670501BR'],
            ['tracking'=>'NC605562154BR','warehouse'=>'TM2595080417BR'],
            ['tracking'=>'NC605561600BR','warehouse'=>'TM2593767341BR'],
            ['tracking'=>'IX030886408BR','warehouse'=>'HD2593097545BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2d() {
        $codes = [
            ['tracking'=>'NC620166677BR','warehouse'=>'TM2593520718BR'],
            ['tracking'=>'NC620166685BR','warehouse'=>'TM2593720846BR'],
            ['tracking'=>'NC605559504BR','warehouse'=>'TM2593720956BR'],
            ['tracking'=>'NC605559518BR','warehouse'=>'TM2593821005BR'],
            ['tracking'=>'NC620166703BR','warehouse'=>'TM2593921305BR'],
            ['tracking'=>'NC605559549BR','warehouse'=>'TM2594021633BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2e() {
        $codes = [
            ['tracking'=>'NC605559725BR','warehouse'=>'TM2595424318BR'],
            ['tracking'=>'NC605559932BR','warehouse'=>'TM2595029906BR'],
            ['tracking'=>'NC605560272BR','warehouse'=>'TM2594635744BR'],
            ['tracking'=>'NC605560286BR','warehouse'=>'TM2594735815BR'],
            ['tracking'=>'NC620167315BR','warehouse'=>'TM2590639230BR'],
            ['tracking'=>'NC620167394BR','warehouse'=>'TM2593340758BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2f() {
        $codes = [
            ['tracking'=>'NC605560737BR','warehouse'=>'TM2593047148BR'],
            ['tracking'=>'NC605560856BR','warehouse'=>'TM2592249828BR'],
            ['tracking'=>'NC605560860BR','warehouse'=>'TM2592349918BR'],
            ['tracking'=>'NC605560975BR','warehouse'=>'TM2593752535BR'],
            ['tracking'=>'NC620167862BR','warehouse'=>'TM2594052733BR'],
            ['tracking'=>'NC605562239BR','warehouse'=>'TM2590682315BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2g() {
        $codes = [
            ['tracking'=>'NC605562242BR','warehouse'=>'TM2590682427BR'],
            ['tracking'=>'NC620169086BR','warehouse'=>'TM2590782717BR'],
            ['tracking'=>'NC620169109BR','warehouse'=>'TM2590883224BR'],
            ['tracking'=>'NC620169143BR','warehouse'=>'TM2590983844BR'],
            ['tracking'=>'NC605562335BR','warehouse'=>'TM2594484755BR'],
            ['tracking'=>'NC605562358BR','warehouse'=>'TM2594685125BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2h() {
        $codes = [
            ['tracking'=>'NC653642303BR','warehouse'=>'TM2595086320BR'],
            ['tracking'=>'NC653642334BR','warehouse'=>'TM2595186756BR'],
            ['tracking'=>'NC653642351BR','warehouse'=>'TM2590188042BR'],
            ['tracking'=>'NC620169395BR','warehouse'=>'TM2590288112BR'],
            ['tracking'=>'NC653642569BR','warehouse'=>'TM2595895458BR'],
            ['tracking'=>'NC620169789BR','warehouse'=>'TM2602300714BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone2i() {
        $codes = [
            ['tracking'=>'NC620169846BR','warehouse'=>'TM2603701921BR'],
            ['tracking'=>'NC653642878BR','warehouse'=>'TM2604302222BR'],
            ['tracking'=>'NC653642952BR','warehouse'=>'TM2604604017BR'],
            ['tracking'=>'NC605559257BR','warehouse'=>'TM2592212752BR'],
            ['tracking'=>'NC605559552BR','warehouse'=>'TM2592121806BR'],
            ['tracking'=>'NC605559566BR','warehouse'=>'TM2592821945BR'],
            ['tracking'=>'NC620169599BR','warehouse'=>'TM2593294554BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }
    // Zone 3
    function bCNToAnjunLabelsZone3a() {
        $codes = [
            ['tracking'=>'NC574413042BR','warehouse'=>'TM2570748009BR'],
            ['tracking'=>'NC550899355BR','warehouse'=>'TM2570748118BR'],
            ['tracking'=>'NC574417755BR','warehouse'=>'TM2583366257BR'],
            ['tracking'=>'NC620165835BR','warehouse'=>'TM2583795742BR'],
            ['tracking'=>'NC620165932BR','warehouse'=>'TM2583895900BR'],
            ['tracking'=>'NC605558693BR','warehouse'=>'TM2583896142BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone3b() {
        $codes = [
            ['tracking'=>'NC574417472BR','warehouse'=>'TM2581359931BR'],
            ['tracking'=>'NC620166663BR','warehouse'=>'TM2593520610BR'],
            ['tracking'=>'NC605559606BR','warehouse'=>'TM2593722301BR'],
            ['tracking'=>'NC620166836BR','warehouse'=>'TM2590224738BR'],
            ['tracking'=>'NC605560290BR','warehouse'=>'TM2594735932BR'],
            ['tracking'=>'NC620167346BR','warehouse'=>'TM2591739920BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone3c() {
        $codes = [
            ['tracking'=>'NC620169112BR','warehouse'=>'TM2591083454BR'],
            ['tracking'=>'NC620169347BR','warehouse'=>'TM2595887332BR'],
            ['tracking'=>'NC620169378BR','warehouse'=>'TM2595987651BR'],
            ['tracking'=>'NC653642475BR','warehouse'=>'TM2591494001BR'],
            ['tracking'=>'NC653642396BR','warehouse'=>'TM2591491703BR'],
            ['tracking'=>'NC653642864BR','warehouse'=>'TM2603902040BR'],
            ['tracking'=>'NC653642904BR','warehouse'=>'TM2603303302BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    // Zone4
    function bCNToAnjunLabelsZone4a() {
        $codes = [
            ['tracking'=>'NC574413100BR','warehouse'=>'TM2571250732BR'],
            ['tracking'=>'NC574417720BR','warehouse'=>'TM2583265810BR'],
            ['tracking'=>'NC620165946BR','warehouse'=>'TM2583996417BR'],
            ['tracking'=>'NC620165844BR','warehouse'=>'TM2584197202BR'],
            ['tracking'=>'NC620165915BR','warehouse'=>'TM2584297708BR'],
            ['tracking'=>'NC620166442BR','warehouse'=>'TM2594811858BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4b() {
        $codes = [
            ['tracking'=>'NC605560445BR','warehouse'=>'TM2595638548BR'],
            ['tracking'=>'NC620167363BR','warehouse'=>'TM2592640428BR'],
            ['tracking'=>'NC620167964BR','warehouse'=>'TM2592355937BR'],
            ['tracking'=>'NC620168315BR','warehouse'=>'TM2591563219BR'],
            ['tracking'=>'NC605561542BR','warehouse'=>'TM2591465830BR'],
            ['tracking'=>'NC605561692BR','warehouse'=>'TM2590669507BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4c() {
        $codes = [
            ['tracking'=>'NC605561732BR','warehouse'=>'TM2593570310BR'],
            ['tracking'=>'NC605561794BR','warehouse'=>'TM2593971557BR'],
            ['tracking'=>'NC605562137BR','warehouse'=>'TM2591780142BR'],
            ['tracking'=>'NC620168956BR','warehouse'=>'TM2593680240BR'],
            ['tracking'=>'NC605562145BR','warehouse'=>'TM2594380321BR'],
            ['tracking'=>'NC620168960BR','warehouse'=>'TM2595980536BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4d() {
        $codes = [
            ['tracking'=>'NC620169625BR','warehouse'=>'TM2591996006BR'],
            ['tracking'=>'NC620169903BR','warehouse'=>'TM2604704131BR'],
            ['tracking'=>'NC653642983BR','warehouse'=>'TM2604504409BR'],
            ['tracking'=>'NC253044180BR','warehouse'=>'TM2600204639BR'],
            ['tracking'=>'NC620166076BR','warehouse'=>'TM2595901035BR'],
            ['tracking'=>'NC620166986BR','warehouse'=>'TM2594427713BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4e() {
        $codes = [
            ['tracking'=>'NC605562327BR','warehouse'=>'TM2594484521BR'],
            ['tracking'=>'NC620166694BR','warehouse'=>'TM2593821113BR'],
            ['tracking'=>'NC605559521BR','warehouse'=>'TM2593821227BR'],
            ['tracking'=>'NC605559535BR','warehouse'=>'TM2593921413BR'],
            ['tracking'=>'NC620166717BR','warehouse'=>'TM2593921521BR'],
            ['tracking'=>'NC620166725BR','warehouse'=>'TM2594121719BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4f() {
        $codes = [
            ['tracking'=>'NC605559610BR','warehouse'=>'TM2593722438BR'],
            ['tracking'=>'NC620166734BR','warehouse'=>'TM2593822500BR'],
            ['tracking'=>'NC620166748BR','warehouse'=>'TM2594022822BR'],
            ['tracking'=>'NC605559645BR','warehouse'=>'TM2594022935BR'],
            ['tracking'=>'NC620166751BR','warehouse'=>'TM2594123035BR'],
            ['tracking'=>'NC605559654BR','warehouse'=>'TM2594223112BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4g() {
        $codes = [
            ['tracking'=>'NC620166765BR','warehouse'=>'TM2594223236BR'],
            ['tracking'=>'NC605559668BR','warehouse'=>'TM2594223357BR'],
            ['tracking'=>'NC605559671BR','warehouse'=>'TM2594523451BR'],
            ['tracking'=>'NC620166779BR','warehouse'=>'TM2594623529BR'],
            ['tracking'=>'NC620166796BR','warehouse'=>'TM2594823703BR'],
            ['tracking'=>'NC605559708BR','warehouse'=>'TM2595024014BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4h() {
        $codes = [
            ['tracking'=>'NC605559711BR','warehouse'=>'TM2595124117BR'],
            ['tracking'=>'NC620166805BR','warehouse'=>'TM2595224236BR'],
            ['tracking'=>'NC620166819BR','warehouse'=>'TM2595524421BR'],
            ['tracking'=>'NC605559739BR','warehouse'=>'TM2595924553BR'],
            ['tracking'=>'NC620166822BR','warehouse'=>'TM2590124608BR'],
            ['tracking'=>'NC620167377BR','warehouse'=>'TM2592940554BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4i() {
        $codes = [
            ['tracking'=>'NC620167403BR','warehouse'=>'TM2593440814BR'],
            ['tracking'=>'NC620167417BR','warehouse'=>'TM2593440931BR'],
            ['tracking'=>'NC605560520BR','warehouse'=>'TM2593541007BR'],
            ['tracking'=>'NC605560710BR','warehouse'=>'TM2592246805BR'],
            ['tracking'=>'NC620167638BR','warehouse'=>'TM2592446930BR'],
            ['tracking'=>'NC620167765BR','warehouse'=>'TM2592550035BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4j() {
        $codes = [
            ['tracking'=>'NC620167788BR','warehouse'=>'TM2594250509BR'],
            ['tracking'=>'NC620167831BR','warehouse'=>'TM2592351954BR'],
            ['tracking'=>'NC605560989BR','warehouse'=>'TM2593752645BR'],
            ['tracking'=>'NC620167902BR','warehouse'=>'TM2590053640BR'],
            ['tracking'=>'NC620168032BR','warehouse'=>'TM2591357702BR'],
            ['tracking'=>'NC620169069BR','warehouse'=>'TM2590582153BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4k() {
        $codes = [
            ['tracking'=>'NC620169072BR','warehouse'=>'TM2590682553BR'],
            ['tracking'=>'NC605562256BR','warehouse'=>'TM2590782600BR'],
            ['tracking'=>'NC605562260BR','warehouse'=>'TM2590782825BR'],
            ['tracking'=>'NC605562287BR','warehouse'=>'TM2590783157BR'],
            ['tracking'=>'NC605562300BR','warehouse'=>'TM2590983734BR'],
            ['tracking'=>'NC620169157BR','warehouse'=>'TM2591084041BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4l() {
        $codes = [
            ['tracking'=>'NC620169191BR','warehouse'=>'TM2594384449BR'],
            ['tracking'=>'NC620169231BR','warehouse'=>'TM2594685249BR'],
            ['tracking'=>'NC620169245BR','warehouse'=>'TM2594785313BR'],
            ['tracking'=>'NC605562361BR','warehouse'=>'TM2594785436BR'],
            ['tracking'=>'NC605562375BR','warehouse'=>'TM2594785553BR'],
            ['tracking'=>'NC620169262BR','warehouse'=>'TM2594885839BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4m() {
        $codes = [
            ['tracking'=>'NC653642317BR','warehouse'=>'TM2595086443BR'],
            ['tracking'=>'NC653642325BR','warehouse'=>'TM2595186509BR'],
            ['tracking'=>'NC620169280BR','warehouse'=>'TM2595186624BR'],
            ['tracking'=>'NC620169364BR','warehouse'=>'TM2595987525BR'],
            ['tracking'=>'NC653642467BR','warehouse'=>'TM2591393909BR'],
            ['tracking'=>'NC653642484BR','warehouse'=>'TM2591494113BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4n() {
        $codes = [
            ['tracking'=>'NC653642816BR','warehouse'=>'TM2602200517BR'],
            ['tracking'=>'NC620169850BR','warehouse'=>'TM2600702518BR'],
            ['tracking'=>'NC620169801BR','warehouse'=>'TM2602501132BR'],
            ['tracking'=>'NC620169863BR','warehouse'=>'TM2600902646BR'],
            ['tracking'=>'NC620169885BR','warehouse'=>'TM2602602922BR'],
            ['tracking'=>'NC653642918BR','warehouse'=>'TM2603603533BR'],
         ];
         return $this->updateTracking($codes, 4, 1);
    }

    function bCNToAnjunLabelsZone4o() {
        $codes = [
            ['tracking'=>'NC653642970BR','warehouse'=>'TM2605604312BR'],
            ['tracking'=>'NC605562070BR','warehouse'=>'TM2593478745BR'],
            ['tracking'=>'NC620169435BR','warehouse'=>'TM2595291114BR'],
            ['tracking'=>'IX030958524BR','warehouse'=>'TM2594096325BR'],
            ['tracking'=>'IX031016076BR','warehouse'=>'TM2602104848BR'],
            ['tracking'=>'IX031016080BR','warehouse'=>'TM2603305035BR'],
            ['tracking'=>'IX031011079BR','warehouse'=>'TM2604705202BR'],
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
