<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Services\Brazil\CN38LabelMaker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeliveryBillDownloadController extends Controller
{
    public function __invoke(DeliveryBill $deliveryBill)
    {
        if ($deliveryBill->containers->isEmpty()) {
            return redirect()->back()->with('error', 'please add a container to this delivery bill');
        }

        $contractNo = ($deliveryBill->containers->first()->hasAnjunService()) ? '9912501700' : '9912501576';
        
            $labelPrinter = new CN38LabelMaker();
            $labelPrinter->setDeliveryBillNo($deliveryBill->cnd38_code)
                        ->setContractNo($contractNo)
                        ->setDate(Carbon::now()->format('Y-m-d'))
                        ->setTime(Carbon::now()->format('h:i'))
                        ->setService(2)
                        ->setTaxModality('ddu')
                        ->setOriginAirpot('MIA')
                        ->setDestinationAirpot('GRU')
                        ->setBags(
                            $deliveryBill->containers
                        )
                        ->setDeliveryBill($deliveryBill);
            return $labelPrinter->download();
    }
}
