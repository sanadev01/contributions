<?php

namespace App\Services\Excel\Import;

use App\Models\Country;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\State;
use App\Services\Excel\AbstractImportService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class OrderImportService extends AbstractImportService
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
        try {
            
            if ( strlen($this->getValue("D{$row}")) <=0 ){
                return;
            }
            
            $order = Order::where('customer_reference',$this->getValue("D{$row}"))->first();
            
            if ( $order ){
                $this->addItem($order,$row);
                $order->doCalculations();
                return;
            }
            
            DB::beginTransaction();
            $shippingService = ShippingService::first();

            $order = Order::create([
                'user_id' => $this->userId,
                'shipping_service_id' => $shippingService->id,
                'shipping_service_name' => $shippingService->name,
                "merchant" => $this->getValue("A{$row}"),
                "carrier" => $this->getValue("B{$row}"),
                "tracking_id" => $this->getValue("C{$row}"),
                "customer_reference" => $this->getValue("D{$row}"),
                "weight" => $this->getValue("E{$row}"),
                "length" => $this->getValue("F{$row}"),
                "width" => $this->getValue("G{$row}"),
                "height" => $this->getValue("H{$row}"),
                "measurement_unit" => $this->getValue("I{$row}"),
                "is_invoice_created" => true,
                "is_shipment_added" => true,
                'status' => Order::STATUS_ORDER,
                'order_date' => now(), 

                "sender_first_name" => $this->getValue("J{$row}"),
                "sender_last_name" => $this->getValue("K{$row}"),
                "sender_email" => $this->getValue("L{$row}"),
                "sender_phone" => $this->getValue("M{$row}")
            ]);

            $order->recipient()->create([
                "first_name" =>$this->getValue("N{$row}"),
                "last_name" => $this->getValue("O{$row}"),
                "email" => $this->getValue("P{$row}"),
                "phone" => $this->getValue("Q{$row}"),
                "address" => $this->getValue("R{$row}"),
                "address2" => $this->getValue("S{$row}"),
                "street_no" => $this->getValue("T{$row}"),
                "zipcode" => $this->getValue("U{$row}"),
                "city" => $this->getValue("V{$row}"),
                "account_type" => 'individual',
                "state_id" => optional( State::where('code',$this->getValue("W{$row}"))->first() )->id,
                "country_id" => optional( Country::where('code',$this->getValue("X{$row}"))->first() )->id,
                "tax_id" => $this->getValue("Y{$row}")
            ]);

            $order->update([
                'warehouse_number' => "TEMPWHR-{$order->id}",
                'user_declared_freight' => $this->getValue("Z{$row}"),
            ]);
            
            $this->addItem($order,$row);
            // $order->doCalculations();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::info($ex);
            \Log::info("{$row}: ".$ex->getMessage());
        }
    }

    public function addItem(Order $order,$row)
    {
        $order->items()->create([
            "quantity" => $this->getValue("AA{$row}"),
            "value" => $this->getValue("AB{$row}"),
            "description" => $this->getValue("AC{$row}"),
            "sh_code" => $this->getValue("AD{$row}"),
            "contains_battery" => strlen($this->getValue("AE{$row}")) >0 ? true : false,
            "contains_perfume" => strlen($this->getValue("AF{$row}")) >0 ? true : false
        ]);
    }
}
