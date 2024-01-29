<?php

namespace App\Services\Excel\Export;

use App\Models\User;
use App\Models\Order;
use App\Models\ShippingService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrderExportTemp extends AbstractExportService
{
    private $orders;
    private $user;
    private $id;

    private $currentRow = 1;

    public function __construct(Collection $orders, $id)
    {
        $this->orders = $orders;
        $this->id = $id;
        $this->authUser = User::find($this->id);

        parent::__construct();
    }

    public function handle()
    {
        $this->prepareExcelSheet();

        return $this->downloadExcel();
    }

    private function prepareExcelSheet()
    {
        $this->setExcelHeaderRow();

        $row = $this->currentRow;


        foreach ($this->orders as $order) {

            if ($order->shippingService->service_sub_class == ShippingService::Post_Plus_Registered || $order->shippingService->service_sub_class == ShippingService::Post_Plus_CO_REG) {
                $type = 'Registered';
            } elseif ($order->shippingService->service_sub_class == ShippingService::Post_Plus_EMS || $order->shippingService->service_sub_class == ShippingService::Post_Plus_CO_EMS) {
                $type = 'EMS';
            } elseif ($order->shippingService->service_sub_class == ShippingService::Post_Plus_Prime) {
                $type = 'Prime';
            } elseif ($order->shippingService->service_sub_class == ShippingService::Post_Plus_Premium) {
                $type = 'ParcelUPU';
            } elseif ($order->shippingService->service_sub_class == ShippingService::LT_PRIME) {
                $type = 'Priority';
            } elseif ($order->shippingService->service_sub_class == ShippingService::Post_Plus_LT_Premium) {
                $type = 'Premium';
            }
            $user = $order->user;
            $this->setCellValue('A' . $row, $order->containers->first()->awb);
            $this->setCellValue('B' . $row, $order->containers->first()->seal_no);
            $this->setCellValue('C' . $row, (string)$this->getOrderTrackingCodes($order));
            // $this->setCellValue('D'.$row, optional($order)->customer_reference);
            $this->setCellValue('D' . $row, '');
            $this->setCellValue('E' . $row, optional($order->recipient)->getFullName());
            $this->setCellValue('F' . $row, optional($order->recipient)->zipcode);
            $this->setCellValue('G' . $row, optional(optional($order->recipient)->state)->name);
            $this->setCellValue('H' . $row, optional($order->recipient)->city);
            $this->setCellValue('I' . $row, optional($order->recipient)->address . ' ' . optional($order->recipient)->street_no);
            $this->setCellValue('J' . $row, optional($order->recipient)->phone . ' ');
            $this->setCellValue('K' . $row, optional($order->recipient)->phone . ' ');
            $this->setCellValue('L' . $row, optional($order->recipient)->email);
            $this->setCellValue('M' . $row, optional(optional($order->recipient)->country)->code);
            $this->setCellValue('N' . $row, '');
            $this->setCellValue('O' . $row, $order->items->first()->description);
            $this->setCellValue('P' . $row, $order->items->first()->sh_code);
            $this->setCellValue('Q' . $row, $order->items->count());
            $this->setCellValue('R' . $row, $order->getOriginalWeight() / $order->items->count());
            $this->setCellValue('S' . $row, $order->getOriginalWeight());
            $this->setCellValue('T' . $row, $order->items->sum('value') / $order->items->count());
            $this->setCellValue('U' . $row, $order->items->sum('value'));
            $this->setCellValue('V' . $row, 'USD');
            $this->setCellValue('W' . $row, $type);
            $this->setCellValue('x' . $row, $order->containers->first()->tax_modality);
            $this->setCellValue('Y' . $row, $order->recipient->tax_id);
            $this->setCellValue('Z' . $row, '');
            $this->setCellValue('AA' . $row, '');
            $this->setCellValue('AB' . $row, 'B2C');
            $this->setCellValue('AC' . $row, $type == 'Priority' || $type == 'Premium' ? 'LTPO' : 'UZPO');
            $this->setCellValue('AD' . $row, $order->carrierService());
            $this->setCellValue('AE' . $row, $type == 'Priority' || $type == 'Premium' ? 'LTPO ' . $type : 'UZPO ' . $type);
            $this->setCellValue('AF' . $row, '');
            $this->setCellValue('AG' . $row, '');
            $this->setCellValue('AH' . $row, '');
            $this->setCellValue('AI' . $row, '');
            $this->setCellValue('AJ' . $row, $order->status == Order::STATUS_CANCEL ? 'TRUE' : 'FALSE');


            $row++;
        }
    }

    private function setExcelHeaderRow()
    {
        $this->setColumnWidth('A', 20);
        $this->setCellValue('A1', 'WAY BILL');

        $this->setColumnWidth('B', 20);
        $this->setCellValue('B1', 'PACKAGE ID');

        $this->setColumnWidth('C', 20);
        $this->setCellValue('C1', 'PARCEL ID');

        $this->setColumnWidth('D', 20);
        $this->setCellValue('D1', 'CLIENT ID');

        $this->setColumnWidth('E', 20);
        $this->setCellValue('E1', 'NAME');

        $this->setColumnWidth('F', 20);
        $this->setCellValue('F1', 'ZIP');

        $this->setColumnWidth('G', 23);
        $this->setCellValue('G1', '	REGION');

        $this->setColumnWidth('H', 25);
        $this->setCellValue('H1', 'CITY');

        $this->setColumnWidth('I', 25);
        $this->setCellValue('I1', 'ADDRESS');

        $this->setColumnWidth('J', 25);
        $this->setCellValue('J1', 'PHONE NUMBER ');

        $this->setColumnWidth('K', 25);
        $this->setCellValue('K1', 'PHONE NORMALIZED');

        $this->setColumnWidth('L', 20);
        $this->setCellValue('L1', 'EMAIL');

        $this->setColumnWidth('M', 20);
        $this->setCellValue('M1', 'COUNTRY');


        $this->setColumnWidth('N', 20);
        $this->setCellValue('N1', 'SKU CODE');

        $this->setColumnWidth('O', 20);
        $this->setCellValue('O1', 'DESCRIPTION OF CONTENT');

        $this->setColumnWidth('P', 20);
        $this->setCellValue('P1', 'HS CODE');

        $this->setColumnWidth('Q', 20);
        $this->setCellValue('Q1', 'QUANTITY');

        $this->setColumnWidth('R', 20);
        $this->setCellValue('R1', 'WEIGHT PER ITEM,KG');

        $this->setColumnWidth('S', 20);
        $this->setCellValue('S1', 'WEIGHT PER PARCEL,KG');

        $this->setColumnWidth('T', 20);
        $this->setCellValue('T1', 'PRICE PER ITEM');

        $this->setColumnWidth('U', 20);
        $this->setCellValue('U1', 'PRICE PER PARCEL');

        $this->setColumnWidth('V', 20);
        $this->setCellValue('V1', 'CURRENCY');

        $this->setColumnWidth('W', 20);
        $this->setCellValue('W1', 'MAIL TYPE');
        $this->setColumnWidth('X', 20);
        $this->setCellValue('X1', 'TAX TYPE');
        $this->setColumnWidth('Y', 20);
        $this->setCellValue('Y1', 'TAX IDENTIFICATION');
        $this->setColumnWidth('Z', 20);
        $this->setCellValue('Z1', 'ROUTE INFO');
        $this->setColumnWidth('AA', 20);
        $this->setCellValue('AA1', 'SHIP DATE');
        $this->setColumnWidth('AB', 20);
        $this->setCellValue('AB1', 'TRANSACTION TYPE');
        $this->setColumnWidth('AC', 20);
        $this->setCellValue('AC1', 'SERVICE CODE');
        $this->setColumnWidth('AD', 20);
        $this->setCellValue('AD1', 'CARRIER SERVICE CODE');
        $this->setColumnWidth('AE', 20);
        $this->setCellValue('AE1', 'CARRIER');
        $this->setColumnWidth('AF', 20);
        $this->setCellValue('AF1', 'MANIFEST PARCEL NR');
        $this->setColumnWidth('AG', 20);
        $this->setCellValue('AG1', 'EXTERNAL ID');
        $this->setColumnWidth('AH', 20);
        $this->setCellValue('AH1', 'WARNING');
        $this->setColumnWidth('AI', 20);
        $this->setCellValue('AI1', 'ERRORS');
        $this->setColumnWidth('AJ', 20);
        $this->setCellValue('AJ1', 'CANCELLED');

        $this->currentRow++;
    }



    public function isWeightInKg($measurement_unit)
    {
        return $measurement_unit == 'kg/cm' ? 'kg' : 'lbs';
    }

    public function chargeWeight($order)
    {
        $getOriginalWeight = $order->getOriginalWeight('kg');
        $chargeWeight = $getOriginalWeight;
        $getWeight = $order->getWeight('kg');
        if ($getWeight > $getOriginalWeight && $order->weight_discount) {
            $discountWeight = $order->weight_discount;
            if ($order->measurement_unit == 'lbs/in') {
                $discountWeight = $order->weight_discount / 2.205;
            }
            $consideredWeight = $getWeight - $getOriginalWeight;
            $chargeWeight = ($consideredWeight - $discountWeight) + $getOriginalWeight;
        }

        return round($chargeWeight, 2);
    }

    private function getOrderTrackingCodes($order)
    {
        $trackingCodes = ($order->has_second_label ? $order->corrios_tracking_code . ',' . $order->us_api_tracking_code : $order->corrios_tracking_code);
        return (string)$trackingCodes;
    }
}
