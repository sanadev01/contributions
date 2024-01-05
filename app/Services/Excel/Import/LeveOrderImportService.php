<?php

namespace App\Services\Excel\Import;

use App\Models\Country;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\State;
use App\Services\Excel\AbstractImportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class LeveOrderImportService extends AbstractImportService
{
    private $userId; 

    public function __construct(UploadedFile $file,$userId)
    {
        $this->userId = $userId;

        $filename = $this->importFile($file);

        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle() 
    {
        $this->importOrders();
    }

    public function importOrders()
    {
        foreach (range(2, $this->noRows) as $row) {
            $this->createOrUpdateOrder($row);
        }

    }

    private function createOrUpdateOrder($row){
        DB::beginTransaction();

        try {
            
            $order = Order::where('corrios_tracking_code',$this->getValue("B{$row}"))->first();

            // Order Already Exists with Tracking
            if ( $order ){
                DB::commit();
                return;
            }

            $shippingService = ShippingService::find(2);

            $order = Order::create([
                'user_id' => $this->userId,
                'shipping_service_id' => $shippingService->id,
                'shipping_service_name' => $shippingService->name,
                "merchant" => 'Leve',
                "carrier" => 'Leve',
                "tracking_id" => '',
                "corrios_tracking_code" => $this->getValue("B{$row}"),
                "customer_reference" => $this->getValue("A{$row}"),
                "weight" => $this->getValue("J{$row}"),
                "length" => $this->getValue("K{$row}"),
                "width" => $this->getValue("M{$row}"),
                "height" => $this->getValue("L{$row}"),
                "measurement_unit" => 'kg/cm',
                "is_invoice_created" => true,
                "is_shipment_added" => true,
                'status' => Order::STATUS_PAYMENT_DONE,
                'order_date' => now(), 
                'order_value' => $this->getValue("O{$row}"), 
                'is_paid' => false, 

                "sender_first_name" => 'Leve',
                "sender_last_name" => 'leve',
                "sender_email" => '',
                "sender_phone" => '',
                'cn23' =>  [
                    "tracking_code" => $this->getValue("B{$row}"),
                    "stamp_url" => "https://api.leveexpress.com/api/operation/download-cn23/{$this->getValue("B{$row}")}",
                ]
            ]);

            $order->recipient()->create([
                "first_name" => $this->getValue("C{$row}"),
                "last_name" => '',
                "email" => '',
                "phone" => '',
                "address" => '',
                "address2" => '',
                "street_no" => '',
                "zipcode" => '',
                "city" => '',
                "account_type" => 'individual',
                "state_id" => optional( State::where('code',$this->getValue("H{$row}"))->first() )->id,
                "country_id" => optional( Country::where('code','BR')->first() )->id,
                "tax_id" => ''
            ]);

            $order->update([
                'warehouse_number' => $order->getTempWhrNumber(true),
            ]);

            $order->doCalculations(false);

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollback();
            // \Log::info("{$row}: ".$ex->getMessage());
            \Log::info($ex);
        }
    }
}
