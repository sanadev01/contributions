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
    function container1()
    {
        $codes = [
            'TM2592092113BR'
        ];
        return $this->updateTracking($codes);
    }
    function container2()
    { 
        $codes = [
            'TM2595493453BR',
            'TM2593837548BR',
        ];
        return $this->updateTracking($codes);
    }

    function container3()
    { 

        $codes = [
            'TM2573482946BR',
            'TM2594849156BR',
            'TM2593164304BR',
            'TM2590964832BR',
            'HD2593070228BR',
            'TM2592797453BR',
            'TM2581388816BR',
            'TM2585590329BR',
            'TM2594237722BR',
            'TM2594757453BR',
            'HD2594562042BR',
            'TM2590876921BR',
            'TM2595377332BR',
            'TM2591494245BR',
        ];
        return $this->updateTracking($codes);
    }

    function container4()
    {  
        $codes =  [
            'TM2554829643BR',
            'TM2590701231BR',
            'TM2592030531BR',
            'TM2592434205BR',
            'TM2594350633BR',
            'TM2590351214BR',
            'TM2590353715BR',
            'TM2591954046BR',
            'TM2594257357BR',
            'TM2593861253BR',
            'TM2592064909BR',
            'TM2590569444BR',
            'TM2594277149BR',
            'TM2593905856BR',
            'TM2593813443BR',
            'TM2593915343BR',
            'TM2595319614BR',
            'TM2594142028BR',
            'TM2591149518BR',
            'TM2592251852BR',
            'TM2591655444BR',
            'TM2593156549BR',
            'TM2594762154BR',
            'TM2592868544BR',
            'TM2592270110BR',
            'TM2595970909BR',
            'TM2590576828BR',
            'TM2590891601BR',
            'TM2595993558BR',
            'TM2603003245BR',
            'TM2594888805BR',
            'TM2592292350BR',
            'TM2590893624BR',
            'TM2592394312BR',
            'TM2593196144BR',
            'TM2605802302BR',
        ];
        return $this->updateTracking($codes);
    }

    function container5()
    {
        $codes = [
           'TM2580354111BR',
           'TM2582225609BR',
           'TM2584052054BR',
           'TM2582874555BR',
           'TM2580480511BR',
           'TM2581385046BR',
           'TM2585974757BR',
           'TM2590919709BR',
           'TM2592155547BR',
           'TM2592255749BR',
           'TM2592355814BR',
           'TM2592456001BR',
           'TM2592456124BR',
           'TM2595462223BR',
           'TM2594475354BR',
           'TM2592255623BR',
           'TM2593756907BR',
           'TM2595375852BR',
        ];
        return $this->updateTracking($codes);
    }

    function container6()
    {
        $codes =  [
            'HD2583072648BR',
            'TM2584274846BR',
            'TM2581274954BR',
            'TM2594309710BR',
            'TM2592111037BR',
            'TM2595153344BR',
            'TM2590255013BR',
            'TM2592258405BR',
            'TM2594561941BR',
            'TM2594765316BR',
            'TM2594869114BR',
            'TM2595375714BR',
            'TM2593178121BR',
            'TM2594309600BR',
            'TM2591355323BR',
            'TM2594261624BR',
            'TM2594561838BR',
            'TM2590762556BR',
            'TM2595171718BR',
            'TM2595872646BR',
            'TM2590372732BR',
            'TM2591172851BR',
            'TM2595075533BR',
            'TM2591475925BR',
            'TM2592688434BR',
            'TM2592792601BR',
            'TM2593092716BR',
            'TM2602000304BR',
            'HD2600002419BR',
            'TM2594090653BR',
            'TM2595193244BR',
            'TM2595796528BR',
            'TM2591296805BR',
            'HD2602202705BR',
        ];
        return $this->updateTracking($codes);
    }

    function container7()
    {
        $codes =  [
            'TM2570394817BR',
            'TM2584080419BR',
            'HD2582472536BR',
            'TM2581280355BR',
            'TM2595538233BR',
            'TM2595154752BR',
            'TM2592556423BR',
            'TM2593059008BR',
            'TM2591262844BR',
            'TM2593967445BR',
            'TM2593770658BR',
            'TM2592573618BR',
            'TM2595776721BR',
            'HD2583494800BR',
            'TM2595538358BR',
            'TM2591457916BR',
            'TM2592564148BR',
            'TM2590271834BR',
            'TM2595191019BR',
            'TM2591591822BR',
            'TM2591892052BR',
            'TM2594993030BR',
            'TM2591193743BR',
            'TM2592794455BR',
            'TM2595689103BR',
            'TM2593394636BR',
            'TM2590295541BR',
            'TM2591195739BR',
            'TM2591895906BR',
            'TM2590296758BR',
            'TM2594598745BR',
            'TM2603803733BR',
        ];
        return $this->updateTracking($codes);
    }

    function container8()
    {
        $codes = [
           'TM2593319252BR',
           'TM2593719507BR',
           'TM2591942502BR',
           'TM2591942607BR',
           'TM2591942711BR',
           'TM2591942816BR',
           'TM2593264512BR',
           'TM2593264617BR',
           'TM2593419348BR',
           'TM2593719402BR',
           'TM2591942920BR',
           'TM2593250347BR',
           'TM2593264408BR',
           'TM2591569735BR',
           'TM2593574119BR',
           'HD2594890908BR',
        ];
        return $this->updateTracking($codes);
    }
    function container9()
    {
        $codes = [
           'TM2545684158BR',
           'TM2554829520BR',
           'TM2554929708BR',
           'TM2565158613BR',
           'TM2593515211BR',
           'HD2594237824BR',
           'TM2595554814BR',
           'TM2590154950BR',
           'TM2590655116BR',
           'TM2593256653BR',
           'TM2591858318BR',
           'TM2592865638BR',
           'TM2594867950BR',
           'TM2594968050BR',
           'TM2594669019BR',
           'TM2591669857BR',
           'TM2591770052BR',
           'TM2593970711BR',
           'TM2591171041BR',
           'TM2594775410BR',
           'TM2595075637BR',
           'TM2595376558BR',
           'TM2592577020BR',
           'HD2584594903BR',
           'TM2593415147BR',
           'TM2594452913BR',
           'TM2594754401BR',
           'TM2591255219BR',
           'TM2593356717BR',
           'TM2593556811BR',
           'TM2592658523BR',
           'TM2590362450BR',
           'TM2591062705BR',
           'TM2591262946BR',
           'TM2591563112BR',
           'TM2591863751BR',
           'TM2594364710BR',
           'TM2594767833BR',
           'TM2594870833BR',
           'TM2591571159BR',
           'TM2593471344BR',
           'TM2593971438BR',
           'TM2590771946BR',
           'TM2590972003BR',
           'TM2593976000BR',
           'TM2591588329BR',
           'TM2591791942BR',
           'TM2592192245BR',
           'TM2595293328BR',
           'TM2595499346BR',
           'TM2592492436BR',
           'TM2595093155BR',
           'TM2593894711BR',
           'TM2592097020BR',
           'TM2593497707BR',
           'TM2590399404BR',
        ];
        return $this->updateTracking($codes);
    }

    function container10()
    {
        $codes = [
           'TM2583065548BR',
           'TM2583165615BR',
           'TM2583466457BR',
           'TM2583566525BR',
           'TM2583466328BR',
           'TM2583270844BR',
           'TM2583270950BR',
           'TM2594411512BR',
           'TM2590814113BR',
           'TM2593431001BR',
           'TM2592947858BR',
           'TM2594052857BR',
           'TM2594853009BR',
           'TM2590953909BR',
           'TM2592565059BR',
           'TM2595176236BR',
           'TM2585998658BR',
           'TM2592401536BR',
           'TM2591908455BR',
           'TM2595510019BR',
           'TM2594227545BR',
           'TM2592337120BR',
           'TM2593237350BR',
           'TM2594141200BR',
           'TM2593841904BR',
           'TM2594243305BR',
           'TM2592446353BR',
           'TM2591848113BR',
           'TM2595862317BR',
           'TM2590962608BR',
           'TM2590691532BR',
           'TM2593592805BR',
           'TM2604303805BR',
        ];
        return $this->updateTracking($codes);
    }

    function container11()
    {
        $codes =
            [
                'TM2582865256BR',
                'TM2582965323BR',
                'TM2583265937BR',
                'TM2583791515BR',
                'TM2583270514BR',
                'TM2581490928BR',
                'TM2580098728BR',
                'TM2580198948BR',
                'TM2591904233BR',
                'TM2595907350BR',
                'TM2590908002BR',
                'TM2590110157BR',
                'TM2595812138BR',
                'TM2590715849BR',
                'TM2592718047BR',
                'TM2593318303BR',
                'TM2594518757BR',
                'TM2595718815BR',
                'TM2590720205BR',
                'TM2594622249BR',
                'TM2594827852BR',
                'TM2591528439BR',
                'TM2590730359BR',
                'TM2590930413BR',
                'TM2594631608BR',
                'TM2591732240BR',
                'TM2591833757BR',
                'TM2592033828BR',
                'TM2592334011BR',
                'TM2594535626BR',
                'TM2592137038BR',
                'TM2592437233BR',
                'TM2594037653BR',
                'TM2594737949BR',
                'TM2593741145BR',
                'TM2594142157BR',
                'TM2593043112BR',
                'TM2595544853BR',
                'TM2591545500BR',
                'TM2595046019BR',
                'TM2590346116BR',
                'TM2593146421BR',
                'TM2593749041BR',
                'TM2590549459BR',
                'TM2593552416BR',
                'TM2590553820BR',
                'TM2594157248BR',
                'TM2593259723BR',
                'TM2593360013BR',
                'HD2593460433BR',
                'TM2591663526BR',
                'TM2592263945BR',
                'TM2594267638BR',
                'TM2591372156BR',
                'TM2593472312BR',
                'TM2592773804BR',
                'TM2594275257BR',
                'TM2595576613BR',
                'HD2582094712BR',
                'TM2585795043BR',
                'TM2580295201BR',
                'TM2584398151BR',
                'TM2590901319BR',
                'TM2591703859BR',
                'TM2594606327BR',
                'TM2590707938BR',
                'TM2591308205BR',
                'TM2590810512BR',
                'TM2592511350BR',
                'TM2590012227BR',
                'TM2593913535BR',
                'TM2592015031BR',
                'TM2591927407BR',
                'TM2590128200BR',
                'TM2593728910BR',
                'TM2594029130BR',
                'TM2594531511BR',
                'TM2594832741BR',
                'TM2590633310BR',
                'TM2592334113BR',
                'TM2592634620BR',
                'TM2590636202BR',
                'TM2590241610BR',
                'TM2592843027BR',
                'TM2593843250BR',
                'TM2595344104BR',
                'TM2592045651BR',
                'TM2591346233BR',
                'TM2592246543BR',
                'TM2592348210BR',
                'TM2592748333BR',
                'TM2595949232BR',
                'TM2591651606BR',
                'TM2592652113BR',
                'TM2593152322BR',
                'TM2595053241BR',
                'TM2593059341BR',
                'TM2593159655BR',
                'TM2593460305BR',
                'TM2591963859BR',
                'TM2592964209BR',
                'HD2592364049BR',
                'TM2592865138BR',
                'TM2593565226BR',
                'TM2592472202BR',
                'TM2592177832BR',
                'TM2594478301BR',
                'TM2595078510BR',
                'TM2595078646BR',
                'TM2594092954BR',
                'TM2594194811BR',
            ];
        return $this->updateTracking($codes);
    }

    function container12()
    {
        $codes =  [
            'TM2583270738BR',
            'TM2583270633BR',
            'TM2581490821BR',
            'TM2584498246BR',
            'TM2583599556BR',
            'TM2591703701BR',
            'TM2592709033BR',
            'TM2594511602BR',
            'TM2592513048BR',
            'TM2593818648BR',
            'TM2594820019BR',
            'TM2592522015BR',
            'TM2592228648BR',
            'TM2593028857BR',
            'TM2591536738BR',
            'TM2591836931BR',
            'TM2595842304BR',
            'TM2593448711BR',
            'TM2595853504BR',
            'TM2592254109BR',
            'TM2593468815BR',
            'TM2591677706BR',
            'TM2593778221BR',
            'TM2581399040BR',
            'TM2581799116BR',
            'TM2582499438BR',
            'TM2595601858BR',
            'TM2590908122BR',
            'TM2590310217BR',
            'TM2595928014BR',
            'TM2593330928BR',
            'TM2593541839BR',
            'TM2594245947BR',
            'TM2592846609BR',
            'TM2593148454BR',
            'TM2593448817BR',
            'TM2593448924BR',
            'TM2595751001BR',
            'TM2595453423BR',
            'TM2593059116BR',
        ];
        return $this->updateTracking($codes);
    }

    function container13()
    {
        $codes =  [
           'TM2582495610BR',
           'TM2584398047BR',
           'TM2595501602BR',
           'TM2595701955BR',
           'TM2590702735BR',
           'TM2595806954BR',
           'TM2593809526BR',
           'TM2590410349BR',
           'TM2591010630BR',
           'TM2592211128BR',
           'TM2590914247BR',
           'TM2590218937BR',
           'TM2595130155BR',
           'TM2592730610BR',
           'TM2592930821BR',
           'TM2593531148BR',
           'TM2594131318BR',
           'TM2595031755BR',
           'TM2595931808BR',
           'TM2590631923BR',
           'TM2591632157BR',
           'TM2590432904BR',
           'TM2591333409BR',
           'TM2592434449BR',
           'TM2591636859BR',
           'TM2594241358BR',
           'TM2594442243BR',
           'TM2593345829BR',
           'TM2591349603BR',
           'TM2595951113BR',
           'TM2591751721BR',
           'TM2593159407BR',
           'TM2593259847BR',
           'TM2593360139BR',
           'TM2590969649BR',
           'TM2591101453BR',
           'TM2590802932BR',
           'TM2591503508BR',
           'TM2593305437BR',
           'TM2593809402BR',
           'TM2591216444BR',
           'TM2590619028BR',
           'TM2593119148BR',
           'TM2595220119BR',
           'TM2595727954BR',
           'TM2593929008BR',
           'TM2594429513BR',
           'TM2592830729BR',
           'TM2593631245BR',
           'TM2591333559BR',
           'TM2591648047BR',
           'TM2594168947BR',
           'HD2591495854BR',
        ];
        return $this->updateTracking($codes);
    }

    function bCNToAnjunLabels() {
        $codes = [
            'HD2602501012BR',
            'TM2583366004BR',
            'TM2583165744BR',
            'TM2584197540BR',
            'TM2583996536BR',
            'TM2592204534BR',
            'TM2593047000BR',
            'TM2591663410BR',
            'TM2591083910BR',
            'TM2594885954BR',
            'TM2584197653BR',
            'TM2584297820BR',
            'TM2594986247BR',
            'TM2595286832BR',
            'TM2602701443BR',
            'TM2590087925BR',
            'TM2604403951BR',

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
