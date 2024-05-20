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
    function container1()
    {
        $codes = [
            'IX030958507BR',
        ];
        return $this->updateTracking($codes);
    }
    function container2()
    {
        $codes = [
            'IX030886371BR',
            'IX030886178BR'
        ];
        return $this->updateTracking($codes);
    }

    function container3()
    {
        $codes = [
            'IX030885420BR',
            'IX030886270BR',
            'IX030886297BR',
            'IX030886323BR',
            'IX030886337BR',
            'IX030886399BR',
            'IX030958011BR',
            'IX030958025BR',
            'IX030958250BR',
            'IX030958382BR',
            'IX030958419BR',
            'IX030958453BR',
            'IX030958467BR',
            'IX030958515BR',
        ];
        return $this->updateTracking($codes);
    }

    function container4()
    {
        $codes =  [
            'NC515475956BR',
            'NC605558866BR',
            'NC605559977BR',
            'NC605560215BR',
            'NC605560887BR',
            'NC605560927BR',
            'NC605561030BR',
            'NC605561065BR',
            'NC605561247BR',
            'NC605561397BR',
            'NC605561499BR',
            'NC605561689BR',
            'NC605562021BR',
            'NC620166252BR',
            'NC620166460BR',
            'NC620166527BR',
            'NC620166646BR',
            'NC620167479BR',
            'NC620167743BR',
            'NC620167828BR',
            'NC620167947BR',
            'NC620167978BR',
            'NC620168222BR',
            'NC620168545BR',
            'NC620168562BR',
            'NC620168580BR',
            'NC620168837BR',
            'NC620169452BR',
            'NC620169554BR',
            'NC620169894BR',
            'NC653642365BR',
            'NC653642405BR',
            'NC653642453BR',
            'NC653642498BR',
            'NC653642612BR',
            'NC653642881BR',
        ];
        return $this->updateTracking($codes);
    }

    function container5()
    {
        $codes = [
            'NC574417163BR',
            'NC575448065BR',
            'NC575449074BR',
            'NC575449919BR',
            'NC575450214BR',
            'NC575450364BR',
            'NC605557830BR',
            'NC605559447BR',
            'NC605561131BR',
            'NC605561145BR',
            'NC605561159BR',
            'NC605561162BR',
            'NC605561176BR',
            'NC605561437BR',
            'NC605561936BR',
            'NC620167955BR',
            'NC620168001BR',
            'NC620168797BR',
        ];
        return $this->updateTracking($codes);
    }

    function container6()
    {
        $codes =  [
            'NC575449851BR',
            'NC605557843BR',
            'NC605557857BR',
            'NC605559169BR',
            'NC605559209BR',
            'NC605561012BR',
            'NC605561114BR',
            'NC605561406BR',
            'NC605561423BR',
            'NC605561511BR',
            'NC605561675BR',
            'NC605561967BR',
            'NC605562052BR',
            'NC620166399BR',
            'NC620167933BR',
            'NC620168196BR',
            'NC620168219BR',
            'NC620168253BR',
            'NC620168620BR',
            'NC620168678BR',
            'NC620168681BR',
            'NC620168695BR',
            'NC620168783BR',
            'NC620168806BR',
            'NC620169418BR',
            'NC620169506BR',
            'NC620169510BR',
            'NC620169761BR',
            'NC620169877BR',
            'NC653642382BR',
            'NC653642440BR',
            'NC653642630BR',
            'NC653642657BR',
            'NC653642895BR',
        ];
        return $this->updateTracking($codes);
    }

    function container7()
    {
        $codes =  [
            'NC574414771BR',
            'NC575450205BR',
            'NC605557786BR',
            'NC605558115BR',
            'NC605560428BR',
            'NC605561088BR',
            'NC605561202BR',
            'NC605561295BR',
            'NC605561445BR',
            'NC605561613BR',
            'NC605561763BR',
            'NC605561865BR',
            'NC605562004BR',
            'NC620165773BR',
            'NC620167292BR',
            'NC620168046BR',
            'NC620168355BR',
            'NC620168633BR',
            'NC620169421BR',
            'NC620169466BR',
            'NC620169483BR',
            'NC620169537BR',
            'NC620169568BR',
            'NC620169585BR',
            'NC653642379BR',
            'NC653642507BR',
            'NC653642572BR',
            'NC653642590BR',
            'NC653642609BR',
            'NC653642643BR',
            'NC653642731BR',
            'NC653642921BR',
        ];
        return $this->updateTracking($codes);
    }

    function container8()
    {
        $codes = [
            'IX030886116BR',
            'IX030886120BR',
            'IX030886218BR',
            'IX030886221BR',
            'IX030886235BR',
            'IX030886249BR',
            'IX030886306BR',
            'IX030886310BR',
            'IX030958201BR',
            'IX030958215BR',
            'IX030958277BR',
            'IX030958351BR',
            'IX030958422BR',
            'IX030958436BR',
            'IX030958440BR',
            'IX030958498BR',
        ];
        return $this->updateTracking($codes);
    }

    function container9()
    {
        $codes = [
            'NC479476979BR',
            'NC522420975BR',
            'NC522420989BR',
            'NC546503127BR',
            'NC605559328BR',
            'NC605560405BR',
            'NC605561091BR',
            'NC605561105BR',
            'NC605561128BR',
            'NC605561216BR',
            'NC605561278BR',
            'NC605561539BR',
            'NC605561635BR',
            'NC605561644BR',
            'NC605561661BR',
            'NC605561715BR',
            'NC605561729BR',
            'NC605561777BR',
            'NC605561785BR',
            'NC605561940BR',
            'NC605561953BR',
            'NC605561984BR',
            'NC605562018BR',
            'NC620165760BR',
            'NC620166513BR',
            'NC620167876BR',
            'NC620167916BR',
            'NC620167920BR',
            'NC620167981BR',
            'NC620167995BR',
            'NC620168050BR',
            'NC620168240BR',
            'NC620168275BR',
            'NC620168284BR',
            'NC620168307BR',
            'NC620168338BR',
            'NC620168386BR',
            'NC620168528BR',
            'NC620168576BR',
            'NC620168593BR',
            'NC620168602BR',
            'NC620168616BR',
            'NC620168647BR',
            'NC620168655BR',
            'NC620168810BR',
            'NC620169404BR',
            'NC620169470BR',
            'NC620169497BR',
            'NC620169545BR',
            'NC620169713BR',
            'NC653642419BR',
            'NC653642436BR',
            'NC653642515BR',
            'NC653642665BR',
            'NC653642688BR',
            'NC653642776BR',
        ];
        return $this->updateTracking($codes);
    }

    function container10()
    {
        $codes = [
            'NC574417702BR',
            'NC574417716BR',
            'NC574417764BR',
            'NC574417778BR',
            'NC575449627BR',
            'NC575449785BR',
            'NC575449794BR',
            'NC605559226BR',
            'NC605559274BR',
            'NC605560003BR',
            'NC605560799BR',
            'NC605560992BR',
            'NC605561009BR',
            'NC605561057BR',
            'NC605561508BR',
            'NC605561975BR',
            'NC620165813BR',
            'NC620166102BR',
            'NC620166371BR',
            'NC620166408BR',
            'NC620166972BR',
            'NC620167261BR',
            'NC620167275BR',
            'NC620167425BR',
            'NC620167465BR',
            'NC620167505BR',
            'NC620167598BR',
            'NC620167669BR',
            'NC620168236BR',
            'NC620168267BR',
            'NC620169449BR',
            'NC620169523BR',
            'NC653642935BR',
        ];
        return $this->updateTracking($codes);
    }

    function container11()
    {
        $codes =
            [
                'NC574417680BR',
                'NC575449595BR',
                'NC575449613BR',
                'NC575450510BR',
                'NC605557684BR',
                'NC605558424BR',
                'NC605558591BR',
                'NC605558605BR',
                'NC605559005BR',
                'NC605559107BR',
                'NC605559124BR',
                'NC605559172BR',
                'NC605559243BR',
                'NC605559331BR',
                'NC605559380BR',
                'NC605559393BR',
                'NC605559416BR',
                'NC605559420BR',
                'NC605559464BR',
                'NC605559597BR',
                'NC605559875BR',
                'NC605559889BR',
                'NC605559950BR',
                'NC605559963BR',
                'NC605560034BR',
                'NC605560082BR',
                'NC605560184BR',
                'NC605560198BR',
                'NC605560207BR',
                'NC605560269BR',
                'NC605560357BR',
                'NC605560365BR',
                'NC605560388BR',
                'NC605560391BR',
                'NC605560533BR',
                'NC605560555BR',
                'NC605560595BR',
                'NC605560635BR',
                'NC605560666BR',
                'NC605560683BR',
                'NC605560697BR',
                'NC605560706BR',
                'NC605560825BR',
                'NC605560839BR',
                'NC605560961BR',
                'NC605561043BR',
                'NC605561233BR',
                'NC605561318BR',
                'NC605561335BR',
                'NC605561370BR',
                'NC605561471BR',
                'NC605561485BR',
                'NC605561627BR',
                'NC605561803BR',
                'NC605561817BR',
                'NC605561879BR',
                'NC605561922BR',
                'NC605561998BR',
                'NC620165739BR',
                'NC620165742BR',
                'NC620165756BR',
                'NC620165787BR',
                'NC620166080BR',
                'NC620166195BR',
                'NC620166266BR',
                'NC620166337BR',
                'NC620166354BR',
                'NC620166425BR',
                'NC620166439BR',
                'NC620166456BR',
                'NC620166473BR',
                'NC620166500BR',
                'NC620166969BR',
                'NC620167010BR',
                'NC620167023BR',
                'NC620167045BR',
                'NC620167125BR',
                'NC620167139BR',
                'NC620167142BR',
                'NC620167173BR',
                'NC620167195BR',
                'NC620167235BR',
                'NC620167448BR',
                'NC620167482BR',
                'NC620167496BR',
                'NC620167522BR',
                'NC620167567BR',
                'NC620167584BR',
                'NC620167607BR',
                'NC620167672BR',
                'NC620167686BR',
                'NC620167730BR',
                'NC620167814BR',
                'NC620167845BR',
                'NC620167859BR',
                'NC620167880BR',
                'NC620168103BR',
                'NC620168125BR',
                'NC620168148BR',
                'NC620168341BR',
                'NC620168369BR',
                'NC620168372BR',
                'NC620168390BR',
                'NC620168409BR',
                'NC620168664BR',
                'NC620168845BR',
                'NC620168854BR',
                'NC620168868BR',
                'NC620168871BR',
                'NC653642422BR',
                'NC653642524BR',
            ];
        return $this->updateTracking($codes);
    }

    function container12()
    {
        $codes =  [
            'NC575449763BR',
            'NC605557707BR',
            'NC605558455BR',
            'NC605558574BR',
            'NC605558720BR',
            'NC605558999BR',
            'NC605559141BR',
            'NC605559230BR',
            'NC605559265BR',
            'NC605559402BR',
            'NC605559455BR',
            'NC605559570BR',
            'NC605559892BR',
            'NC605559901BR',
            'NC605560326BR',
            'NC605560343BR',
            'NC605560578BR',
            'NC605560811BR',
            'NC605561026BR',
            'NC605561074BR',
            'NC605561658BR',
            'NC605562049BR',
            'NC605562066BR',
            'NC620165950BR',
            'NC620165963BR',
            'NC620165994BR',
            'NC620166120BR',
            'NC620166345BR',
            'NC620166411BR',
            'NC620167006BR',
            'NC620167108BR',
            'NC620167451BR',
            'NC620167575BR',
            'NC620167615BR',
            'NC620167690BR',
            'NC620167712BR',
            'NC620167726BR',
            'NC620167791BR',
            'NC620167893BR',
            'NC620168085BR',
        ];
        return $this->updateTracking($codes);
    }

    function container13()
    {
        $codes =  [
            'NC605558557BR',
            'NC605558565BR',
            'NC605558870BR',
            'NC605558883BR',
            'NC605558937BR',
            'NC605559098BR',
            'NC605559155BR',
            'NC605559186BR',
            'NC605559190BR',
            'NC605559212BR',
            'NC605559288BR',
            'NC605559433BR',
            'NC605559946BR',
            'NC605559985BR',
            'NC605559994BR',
            'NC605560017BR',
            'NC605560025BR',
            'NC605560048BR',
            'NC605560051BR',
            'NC605560065BR',
            'NC605560079BR',
            'NC605560140BR',
            'NC605560167BR',
            'NC605560238BR',
            'NC605560330BR',
            'NC605560547BR',
            'NC605560564BR',
            'NC605560670BR',
            'NC605560842BR',
            'NC605560913BR',
            'NC605560958BR',
            'NC605561304BR',
            'NC605561321BR',
            'NC605561349BR',
            'NC605561701BR',
            'NC620166093BR',
            'NC620166164BR',
            'NC620166181BR',
            'NC620166249BR',
            'NC620166385BR',
            'NC620166561BR',
            'NC620166629BR',
            'NC620166632BR',
            'NC620166650BR',
            'NC620166990BR',
            'NC620167037BR',
            'NC620167068BR',
            'NC620167099BR',
            'NC620167111BR',
            'NC620167156BR',
            'NC620167655BR',
            'NC620168559BR',
            'NC620169639BR',
        ];
        return $this->updateTracking($codes);
    }

    function updateTracking($codes)
    {
        set_time_limit(3000);
        //update standard
        $ordersDetails = null;
        try {
            foreach ($codes as $code) {
                $flag = false;

                if (Order::where('corrios_tracking_code', $code)
                    ->where('corrios_tracking_code', 'like', 'IX%')
                    ->update(['shipping_service_id' => 44])
                ) {
                    $flag = true;
                }
                if (Order::where('corrios_tracking_code', $code)
                    ->where('corrios_tracking_code', 'like', 'NC%')
                    ->update(['shipping_service_id' => 43])
                ) {
                    $flag = true;
                }


                if (!$flag) {
                    $ordersDetails[] = [
                        'tracking_old' => $code,
                        'warehouse' => 'not found',
                        'tracking_new' => 'not found',
                        'link' => 'not found',
                        'poboxName' => 'not found',

                    ];
                }

                $order = Order::where('corrios_tracking_code', $code)->first();
                $oldTracking = $order->corrios_tracking_code;
                $corrieosBrazilLabelRepository = new CorrieosBrazilLabelRepository();
                $corrieosBrazilLabelRepository->run($order, true);
                if ($order->corrios_tracking_code) {
                    $ordersDetails[] = [
                        'tracking_old' => $oldTracking,
                        'warehouse' => $order->warehouse_number,
                        'tracking_new' => $order->corrios_tracking_code,
                        'link' => route('order.label.download',encrypt($order->id)),
                        'poboxName' => $order->user->pobox_name,
                    ];
                    \Log::info([
                        'tracking_old' => $oldTracking,
                        'warehouse' => $order->warehouse_number,
                        'tracking_new' => $order->corrios_tracking_code,
                        'link' => route('order.label.download',encrypt($order->id)),
                        'poboxName' => $order->user->pobox_name,
                    ]);
                }
            }
        } catch (Exception $e) {
            \Log::info(['error' => $e->getMessage()]);
        }
        if ($ordersDetails) {
            $exports = new OrderUpdateExport($ordersDetails);
            return response()->download($exports->handle());
        }
    }
}
