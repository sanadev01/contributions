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

class ShopifyOrderImportService extends AbstractImportService
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
        $totalError = 0;
        foreach (range(2, $this->noRows) as $row) {
            
            $order = $this->createOrUpdateOrder($row, $importOrder);
            if($order){
                $order->error ? $totalError++ : $totalOrder++;
            }
        }
        
        $importOrder->update([
            'total_orders' => $totalOrder,
            'total_errors' => $totalError
        ]);
        
    }

    private function createOrUpdateOrder($row, $importOrder){
        try {
                if ( strlen($this->getValue("D{$row}")) <=0 ){
                    return;
                }
                
                $findOrder = ImportedOrder::where('customer_reference',$this->getValue("D{$row}"))->first();

                if ( $findOrder ){
                    $order = $this->addItem($findOrder,$row);
                    
                    // $order->doCalculations();
                    return $order;
                }

                unset($this->errors);
                $this->errors = array();

                $this->validationRow($row, false);
            
                // if($this->errors == null){
                \Log::info('sender number shopify'.$this->getValue("M{$row}"));
                DB::beginTransaction();
                $shippingService = ShippingService::where('service_sub_class', $this->correosShippingServices())->first();
                
                $orderError = null;
                if(!empty($this->errors)){
                    $orderError = $this->errors;
                }
                $order = ImportedOrder::create([
                    'user_id' => $this->userId,
                    'import_id' => $importOrder->id,
                    'shipping_service_id' => $shippingService->id,
                    'shipping_service_name' => $shippingService->name,
                    "merchant" => $this->getValue("A{$row}"),
                    "carrier" => $this->getValue("B{$row}"),
                    "tracking_id" => $this->getValue("C{$row}"),
                    "customer_reference" => $this->getValue("D{$row}"),
                    "weight" => $this->getValue("E{$row}")?$this->getValue("E{$row}"):0,
                    "length" => $this->getValue("F{$row}")?$this->getValue("F{$row}"):0,
                    "width" => $this->getValue("G{$row}")?$this->getValue("G{$row}"):0,
                    "height" => $this->getValue("H{$row}")?$this->getValue("H{$row}"):0,
                    "measurement_unit" => $this->getValue("I{$row}")?$this->getValue("I{$row}"):'lbs/in',
                    "is_invoice_created" => true,
                    "is_shipment_added" => true,
                    'status' => Order::STATUS_ORDER,
                    'order_date' => now(), 

                    "sender_first_name" => $this->getValue("J{$row}"),
                    "sender_last_name" => $this->getValue("K{$row}"),
                    "sender_email" => $this->getValue("L{$row}"),
                    "sender_phone" => $this->getValue("M{$row}"),
                
                
                
                    'user_declared_freight' => $this->getValue("Z{$row}"),
                    'error' => $orderError,

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
                        "country_id" => optional( Country::where('name',$this->getValue("X{$row}"))->first() )->id,
                        "tax_id" => $this->getValue("Y{$row}"),
                    ],

                ]);
                
                $order = $this->addItem($order,$row);
                
                // $order->doCalculations();
                DB::commit();
                return $order;
            
            
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
        
        $orderError = null;
        if(!empty($this->errors)){
            $orderError = $this->errors;
        }

        $order->update([
            'items' => $items,
            'error' => $orderError,
        ]);
        
        return $order;
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
        if($item == true){
            return [
                'quantity' => 'required',
                'value' => 'required',
                'description' => 'required',
                'sh_code' => 'required',
            ];
        }

        if($item == false){
            $rules = [
                'merchant' => 'required',
                'carrier' => 'required',
                'tracking_id' => 'required',
                'customer_reference' => 'nullable',
                'weight' => 'required|numeric|gt:0',
                'measurement_unit' => 'required|in:kg/cm,lbs/in',
                'length' => 'required|numeric|gt:0',
                'width' => 'required|numeric|gt:0',
                'height' => 'required|numeric|gt:0',

                'sender_first_name' => 'required',
                'sender_last_name' => 'nullable',
                'sender_email' => 'nullable',
                'sender_phone' => 'nullable|max:15',
                
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
                    'required','max:15','min:13', new PhoneNumberValidator(optional( Country::where('name',$this->getValue("X{$row}"))->first() )->id)
                ],
                'zipcode' => [
                    'required', new ZipCodeValidator(optional( Country::where('name',$this->getValue("X{$row}"))->first() )->id,optional( State::where('code',$this->getValue("W{$row}"))->first() )->id)
                ],

            ];

            if (Country::where('code', 'BR')->first()->id == optional( Country::where('name',$this->getValue("X{$row}"))->first() )->id ) {
                // $rules['recipient_tax_id'] = ['required', "in:cpf,cnpj,CPF,CNPJ"];
                $rules['cpf'] = 'sometimes|cpf|required_if:country_id,'.Country::where('code', 'BR')->first()->id;
            }

            return $rules;

        }
    }

    public function validationMessages($row, $item)
    {
        if($item == true){
            
            return [
                'quantity.required' => 'quantity is required',
                'value.required' => 'value is required',
                'description.required' => 'Product name description required',
                'sh_code.required' => 'NCM sh code is required',
            ];
        }

        if($item == false){
            return [
                'merchant.required' => 'merchant is required',
                'carrier.required' => 'carrier is required',
                'tracking_id.required' => 'tracking id is required',
                'customer_reference.nullable' => 'customer reference is invalid',
                'measurement_unit.required' => 'measurement unit is required',
                'weight.required' => 'weight is required',
                'length.required' => 'length is required',
                'width.required' => 'width is required',
                'height.required' => 'height is required',

                'sender_first_name.required' => 'sender first name is required',
                'sender_last_name.nullable' => 'sender last name is required',
                'sender_email.required' => 'sender Email is required',
                'sender_phone.nullable' => 'sender phone is invalid',
                
                'first_name.required' => 'first Name is required',
                'last_name.required' => 'last Name is required',
                'email.nullable' => 'email is not valid',
                'phone.required' => 'phone is required',
                'address.required' => 'address is required',
                'address2.nullable' => 'Address2 is not more then 50 character',
                'street_no.required' => 'house street no is required',
                'city.required' => 'city is required',
                'state_id.required' => 'state id is required',
                'country_id.required' => 'country is required',
                'zipcode.required' => 'zipcode is required',
                'recipient_tax_id.required' => 'The selected recipient tax id is invalid.',
            ];
        }
    }
    
    public function validationData($row, $item)
    {
        if($item == true){
            
            return [
                "quantity" => $this->getValue("AA{$row}"),
                "value" => $this->getValue("AB{$row}"),
                "description" => $this->getValue("AC{$row}"),
                "sh_code" => $this->getValue("AD{$row}"),
            ];
        }

        if($item == false){
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
                "country_id" => optional( Country::where('name',$this->getValue("X{$row}"))->first() )->id,
                "zipcode" => $this->getValue("U{$row}"),
                "recipient_tax_id" => $this->getValue("Y{$row}"),
            ];
        }

    }

    public function validationRow($row, $item)
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

    private function correosShippingServices()
    {
        if (setting('anjun_api', null, \App\Models\User::ROLE_ADMIN) || setting('bcn_api', null, \App\Models\User::ROLE_ADMIN)) {
            
            if ($this->request->service_id == ShippingService::Packet_Standard) {
                return ShippingService::AJ_Packet_Standard;
            }

            if ($this->request->service_id == ShippingService::Packet_Express) {
                return ShippingService::AJ_Packet_Express;
            }
        }
        return $this->request->service_id;
    }
}
