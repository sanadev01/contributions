<?php

namespace App\Services\Excel\Import;

use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\ImportOrder;
use App\Models\ImportedOrder;
use App\Models\ShippingService;
use App\Rules\ZipCodeValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Rules\PhoneNumberValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\AbstractImportService;

class OrderImportService extends AbstractImportService
{
    private $userId; 
    private $request;
    private $errors = []; 

    public function __construct(UploadedFile $file,$request)
    {
        $this->userId = Auth::id();
        $this->request = $request;

        $filename = $this->importFile($file);
        
        parent::__construct(
            $this->getStoragePath($filename)
        );
    }

    public function handle() 
    {
        
        $importOrder = $this->storeImportOrder();
        
        $this->importOrders($importOrder);

        return $importOrder;
    }

    public function importOrders($importOrder)
    {
        $totalOrder = 0;
        foreach (range(2, $this->noRows) as $row) {
            
           $total = $this->createOrUpdateOrder($row, $importOrder);
           $total? $totalOrder++: $totalOrder;
        }

        $importOrder->update([
            'total_orders' => $totalOrder
        ]);
        
    }

    private function createOrUpdateOrder($row, $importOrder){
        try {
            
            
            if ( strlen($this->getValue("D{$row}")) <=0 ){
                 
                return;
            }
            
            $findOrder = ImportedOrder::where('customer_reference',$this->getValue("D{$row}"))->first();

            if ( $findOrder ){
                
                $this->addItem($findOrder,$row);
                
                // $order->doCalculations();
                return;
            }

            $this->validationRow($row);
            
            if($this->errors == null){

                DB::beginTransaction();
                $shippingService = ShippingService::first();
                
                $order = ImportedOrder::create([
                    'user_id' => $this->userId,
                    'import_id' => $importOrder->id,
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
                    "sender_phone" => $this->getValue("M{$row}"),
                
                
                
                    'user_declared_freight' => $this->getValue("Z{$row}"),

                    'recipient' => [
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
                        "tax_id" => $this->getValue("Y{$row}"),
                    ],

                ]);
                
                $this->addItem($order,$row);
                // $order->doCalculations();
                DB::commit();
                return true;
            }
            return;
            
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::info($ex);
            \Log::info("{$row}: ".$ex->getMessage());
        }
    }

    public function addItem($order,$row)
    {
        $this->validationRow($row, true);

        $item =[
            "quantity" => $this->getValue("AA{$row}"),
            "value" => $this->getValue("AB{$row}"),
            "description" => $this->getValue("AC{$row}"),
            "sh_code" => $this->getValue("AD{$row}"),
            "contains_battery" => strlen($this->getValue("AE{$row}")) >0 ? true : false,
            "contains_perfume" => strlen($this->getValue("AF{$row}")) >0 ? true : false
        ];
        
        $items = $order->items ? $order->items : [];

        array_push($items, $item);

        $order->update([
            'items' => $items
        ]);
        
    }
 
    public function storeImportOrder()
    {
        
        $importOrder = ImportOrder::create([
            'user_id' => Auth::id(),
            'file_name' => $this->request->excel_name,
            'upload_path' => $this->file,
        ]);

        return $importOrder;
    }

    // this function returns all validation errors after import:
    public function getErrors()
    {
        
        return $this->errors;
    }

    public function rules($row, $item): array
    {
        if($item){
            return [
                'quantity' => 'required',
                'value' => 'required',
                'description' => 'required',
                'sh_code' => 'required',
            ];
        }

        return [
            'merchant' => 'required',
            'carrier' => 'required',
            'tracking_id' => 'required',
            'customer_reference' => 'required',
            'weight' => 'required|numeric|gt:0',
            'measurement_unit' => 'required|in:kg/cm,lbs/in',
            'length' => 'required|numeric|gt:0',
            'width' => 'required|numeric|gt:0',
            'height' => 'required|numeric|gt:0',

            'sender_first_name' => 'required',
            'sender_last_name' => 'nullable',
            'sender_email' => 'required',
            'sender_phone' => 'required',
            
            
            'first_name' => 'required|max:100',
            'last_name' => 'max:100',
            'email' => 'nullable|max:100',
            'address' => 'required',
            'address2' => 'nullable|max:50',
            'street_no' => 'required',
            'city' => 'required',
            'state_id' => 'required|exists:states,id',
            'country_id' => 'required|exists:countries,id',

            'phone' => [
                'required','max:15','min:13', new PhoneNumberValidator(optional( Country::where('code',$this->getValue("X{$row}"))->first() )->id)
            ],
            'zipcode' => [
                'required', new ZipCodeValidator(optional( Country::where('code',$this->getValue("X{$row}"))->first() )->id,optional( State::where('code',$this->getValue("W{$row}"))->first() )->id)
            ],
                
        ];

    }

    public function validationMessages($row, $item)
    {
        if($item){
            return [
                'quantity' => 'sh code is required at row ' . $row,
                'value' => 'value is required at row ' . $row,
                'description' => 'Product name required at row ' . $row,
                'sh_code' => 'NCM is required at row ' . $row,
            ];
        }

        return [
            'merchant.required' => 'merchant is required at row '.$row,
            'carrier.required' => 'carrier is required at row '.$row,
            'tracking_id.required' => 'tracking id is required at row '.$row,
            'customer_reference.required' => 'customer reference is required at row '.$row,
            'measurement_unit.required' => 'measurement unit is required at row '.$row,
            'weight.required' => 'weight is required at row '.$row,
            'length.required' => 'length is required at row '.$row,
            'width.required' => 'width is required at row '.$row,
            'height.required' => 'height is required at row '.$row,

            'sender_first_name.required' => 'Sender first name is required at row '.$row,
            'sender_last_name.nullable' => 'Sender last name is required at row '.$row,
            'sender_email.required' => 'Sender Email is required at row '.$row,
            'sender_phone.required' => 'Sender phone is required at row '.$row,
            
            'first_name.required' => 'First Name is required at row '.$row,
            'last_name.required' => 'Last Name is required at row '.$row,
            'email.nullable' => 'Email is not valid at row '.$row,
            'phone.required' => 'Phone is required at row '.$row,
            'address.required' => 'Sender phone is required at row '.$row,
            'address2.nullable' => 'Address2 is not more then 50 character at row '.$row,
            'street_no.required' => 'Sender phone is required at row '.$row,
            'city.required' => 'Sender phone is required at row '.$row,
            'state_id.required' => 'Sender phone is required at row '.$row,
            'country_id.required' => 'Sender phone is required at row '.$row,
            'zipcode.required' => 'Sender phone is required at row '.$row,
        ];

        
    }
    
    public function validationData($row, $item)
    {
        if($item){
            return [
                "quantity" => $this->getValue("AA{$row}"),
                "value" => $this->getValue("AB{$row}"),
                "description" => $this->getValue("AC{$row}"),
                "sh_code" => $this->getValue("AD{$row}"),
            ];
        }

        return [
            "merchant" => $this->getValue("A{$row}"),
            "carrier" => $this->getValue("B{$row}"),
            "tracking_id" => $this->getValue("C{$row}"),
            "customer_reference" => $this->getValue("D{$row}"),
            "measurement_unit" => $this->getValue("I{$row}"),
            "weight" => $this->getValue("E{$row}"),
            "length" => $this->getValue("F{$row}"),
            "width" => $this->getValue("G{$row}"),
            "height" => $this->getValue("H{$row}"),

            "sender_first_name" => $this->getValue("J{$row}"),
            "sender_last_name" => $this->getValue("K{$row}"),
            "sender_email" => $this->getValue("L{$row}"),
            "sender_phone" => $this->getValue("M{$row}"),

            "first_name" =>$this->getValue("N{$row}"),
            "last_name" => $this->getValue("O{$row}"),
            "email" => $this->getValue("P{$row}"),
            "phone" => $this->getValue("Q{$row}"),
            "address" => $this->getValue("R{$row}"),
            "address2" => $this->getValue("S{$row}"),
            "street_no" => $this->getValue("T{$row}"),
            "city" => $this->getValue("V{$row}"),
            "state_id" => optional( State::where('code',$this->getValue("W{$row}"))->first() )->id,
            "country_id" => optional( Country::where('code',$this->getValue("X{$row}"))->first() )->id,
            "zipcode" => $this->getValue("U{$row}"),
        ];

    }

    public function validationRow($row, $item = false)
    {
        $validator = Validator::make($this->validationData($row, $item), $this->rules($row,$item ), $this->validationMessages($row,$item));
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $messages) {
                foreach ($messages as $error) {
                    // accumulating errors:
                    $this->errors[] = $error;
                }
            }
            $this->errors;
        }
    }
}
