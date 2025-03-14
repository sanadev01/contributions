<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Excel\Export\TempOrderExport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class TempOrderReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    { 
        $orders = [
            "IX009819954BR",
            "IX009819971BR",
            "IX009820008BR",
            "IX011119156BR",
            "IX017505277BR",
            "IX019185417BR",
            "NA016346305BR",
            "NA022094265BR",
            "NA028524612BR",
            "NA028527242BR",
            "NA038282703BR",
            "NA038283434BR",
            "NA038283465BR",
            "NA038283686BR",
            "NA038283709BR",
            "NA038283712BR",
            "NA038284094BR",
            "NA038284125BR",
            "NA038284148BR",
            "NA038284165BR",
            "NA038284182BR",
            "NA057745369BR",
            "NA057745837BR",
            "NA057745845BR",
            "NA057745885BR",
            "NA057746015BR",
            "NA057746072BR",
            "NA057746090BR",
            "NA057746109BR",
            "NA057746112BR",
            "NA057746228BR",
            "NA057746713BR",
            "NA061579861BR",
            "NA061579950BR",
            "NA061580003BR",
            "NA061580017BR",
            "NA061580051BR",
            "NA061580065BR",
            "NA061580079BR",
            "NA061580082BR",
            "NA061580096BR",
            "NA061580105BR",
            "NA061580119BR",
            "NA061580122BR",
            "NA061580140BR",
            "NA061592315BR",
            "NA086381502BR",
            "NA100497013BR",
            "NA108998775BR",
            "NA120293734BR",
            "NA128981442BR",
            "NA150836690BR",
            "NA150837372BR",
            "NA167285086BR",
            "NA167287626BR",
            "NA167288312BR",
            "NA176728065BR",
            "NA176729018BR",
            "NA188816995BR",
            "NA188817050BR",
            "NA207469995BR",
            "NA207471089BR",
            "NA207472172BR",
            "NA207474289BR",
            "NA207475806BR",
            "NA207475871BR",
            "NA207475908BR",
            "NA207475942BR",
            "NA210210361BR",
            "NA210211733BR",
            "NA210212963BR",
            "NA210213439BR",
            "NA210213677BR",
            "NA271214614BR",
            "NA277480153BR",
            "NA278684997BR",
            "NA278685961BR",
            "NA281722236BR",
            "NA281723846BR",
            "NA281723917BR",
            "NA281723934BR",
            "NA281726272BR",
            "NA281726286BR",
            "NA392716163BR",
            "NA398563077BR",
            "NA410223900BR",
            "NL174035999BR",
            "NX008479225BR",
            "NX101269573BR",
            "NX280278034BR",
            "NX549791215BR",
            "NX549791229BR",
            "NX549791232BR",
            "NX549792207BR",
            "NX580882827BR",
            "NX625363775BR",
            "NX625363798BR",
            "NX625365025BR",
            "NX625365670BR",
            "NX641875362BR",
            "NX705276647BR",
            "NX716538658BR",
            "NX727411278BR",
            "NX727413645BR",
            "NX737653795BR",
            "NX766574613BR",
            "NX766578147BR",
            "NX766579377BR",
            "NX807299538BR",
            "NX807301316BR",
            "NX814195405BR",
            "NX905282890BR",
            "NX914375572BR",
            "NX914377131BR",
            "NX924674810BR",
            "NX927440456BR",
            "NX927441315BR",
            "NX961248263BR",
            "NX961248665BR",
            "NX961248688BR",
            "NX961248970BR",
            "NX961248983BR",
            "NX961249017BR",
            "NX986159564BR",
            "NX986160341BR",
            "NX986160355BR",
            "NX986160372BR",
            "NX986163285BR",
            "NX997457348BR",
            "NX824216269BR",
            "NX824215192BR",
            "NX807299011BR",
            "NX807297470BR",
            "NX766577699BR",
            "NX716538777BR",
            "NX673963338BR",
            "NX673963267BR",
            "NX668656618BR",
            "NX549790965BR",
            "NX504401541BR",
            "NX456445514BR",
            "NA317878229BR",
            "NA207476545BR",
            "NA191692690BR",
            "NA176727140BR",
            "NA176727048BR",
            "NA167292100BR",
            "NA128983474BR",
            "NX807300488BR",
            "NX766577739BR",
            "NX448293763BR",
            "NA210204746BR",
            "NA207465667BR",
            "NA207463065BR",
            "NA207463017BR",
            "NA176725767BR",
            "NA167290792BR",
            "NA141312941BR",
            "NA100497720BR",
            "NX879299529BR",
            "NX879299285BR",
            "NA210203374BR",
            "NA210201529BR",
            "NA191694775BR",
            "NA188819325BR",
            "NA188816567BR",
            "NA167292949BR",
            "NA167290789BR",
            "NA167290761BR",
            "NA167284871BR",
            "NA167284837BR",
            "NA167284766BR",
            "NA141313037BR",
            "NA141312990BR",
            "NA141312297BR",
            "NA061587029BR",
            "NA038278669BR",
            "NA281726666BR",
            "NA281726652BR",
            "NA210213986BR",
            "NA210213663BR",
            "NA210213575BR",
            "NA210212861BR",
            "NA210211781BR",
            "NA210210335BR",
            "NA210203388BR",
            "NA207476109BR",
            "NA207474522BR",
            "NA207474394BR",
            "NA207473915BR",
            "NA207471659BR",
            "NA207469465BR",
            "NA207468045BR",
            "NA120290168BR",
            "NA061589603BR",
            "NA061588466BR",
            "NA311057295BR",
            "NA287301364BR",
            "NA287297642BR",
            "NA188817341BR",
            "NX879299339BR",
            "NX843162841BR",
            "NX814197931BR",
            "NX807300430BR",
            "NX497318475BR",
            "NA331010332BR",
            "NA120292169BR",
            "NX690387879BR",
            "NX448291983BR",
            "NA128981663BR",
            "NA128981500BR",
            "NA108999532BR",
            "NA108998735BR",
            "NA108995685BR",
            "NA100500392BR",
            "NA100500389BR",
            "NA100497225BR",
            "NA061586187BR",
            "NA061581692BR",
            "NX961249079BR",
            "NX927434610BR",
            "NA108999529BR",
            "NX532523665BR",
            "NX478492079BR",
            "NX456449493BR",
            "IX017505515BR",
        ];

        $orderModels = Order::withTrashed()->whereIn('corrios_tracking_code', $orders)->get();

        $orderData = collect($orders)->map(function ($orderCode) use ($orderModels) {
            $order = $orderModels->firstWhere('corrios_tracking_code', $orderCode);
            if ($order) {
                return $order;
            } else {
                return (object) [
                    'corrios_tracking_code' => $orderCode,
                    'warehouse_number' => '',
                    'user' => (object) ['pobox_number' => ''],
                    'deleted_at' => null,
                ];
            }
        });

        $export = new TempOrderExport($orderData);
        $export->handle();

        return $export->download();
    }

}

