<?php

namespace App\Http\Controllers\Warehouse;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Excel\Export\DeliveryBillExport;
use App\Repositories\Warehouse\DeliveryBillRepository;
use App\Services\Correios\Services\Brazil\CN38LabelMaker;

class DeliveryBillDownloadController extends Controller
{
    public function show(DeliveryBill $download)
    {
        $deliveryBill = $download;
        if ($deliveryBill->containers->isEmpty()) {
            return redirect()->back()->with('error', 'please add a container to this delivery bill');
        }

        $hasAnjunService = $deliveryBill->containers->first()->has_anjun_service || $deliveryBill->containers->first()->has_anjun_china_service;
        $contractNo = $hasAnjunService ? '9912501700' : '9912501576';

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
        if ($hasAnjunService) {
            $labelPrinter = $labelPrinter->setLogo(public_path('images/anjunlog.png'));
            $labelPrinter = $labelPrinter->setOriginLogo("ANJUNLOG");
        }

        return $labelPrinter->download();
    }

    public function create(Request $request, DeliveryBillRepository $deliveryBillRepository)
    {
        $deliveryBills = $deliveryBillRepository->get($request, false);
        $exportService = new DeliveryBillExport($deliveryBills);
        return $exportService->handle();
    }
}
