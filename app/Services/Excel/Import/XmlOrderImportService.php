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

class XmlOrderImportService
{
    private $userId; 
    private $request;
    private $errors = []; 

    public function __construct($request)
    {
        $this->userId = Auth::id();
        $this->request = $request;

        $filename = $this->importFile($request->file('excel_file'));
        $filePath = $this->getStoragePath($filename);
        $this->filePath = $filePath;
    }

    public function handle() 
    {
        $xmlString = file_get_contents($this->filePath);
        $xmlObject = simplexml_load_string($xmlString);
                   
        $json = json_encode($xmlObject);
        $getRecord = json_decode($json, true); 
        $importOrder = $this->storeImportOrder();
        $this->importOrder($getRecord, $importOrder);
    }

    public function importOrder($data,$importOrder)
    {
        foreach($data['Orders'] as $items){
            if(isset($items['Merchant'])){
                $this->createOrUpdateOrder($items,$importOrder);
            }else{
                foreach($items as $records){
                    $this->createOrUpdateOrder($records,$importOrder);
                }
            }
        }
    }

    private function createOrUpdateOrder($items, $importOrder)
    {
        
        try {
                unset($this->errors);
                $this->errors = array();

                $this->validationRow($items, false);
            
                DB::beginTransaction();
                $shippingService = ShippingService::where('service_sub_class', $this->correosShippingServices())->first();
                
                $orderError = null;
                if(!empty($this->errors)){
                    $orderError = $this->errors;
                }

                $order = ImportedOrder::create([
                    'user_id'               => $this->userId,
                    'import_id'             => $importOrder->id,
                    'shipping_service_id'   => $shippingService->id,
                    'shipping_service_name' => $shippingService->name,
                    "merchant"              => $items['Merchant']?$items['Merchant']:null,
                    "carrier"               => $items['Carrier']?$items['Carrier']:null,
                    "tracking_id"           => $items['TrackingId']?$items['TrackingId']:null,
                    "customer_reference"    => $items['CustomerRefrence']?$items['CustomerRefrence']:null,
                    "weight"                => $items['Weight']?$items['Weight']:null,
                    "length"                => $items['Length']?$items['Length']:null,
                    "width"                 => $items['Width']?$items['Width']:null,
                    "height"                => $items['Height']?$items['Height']:null,
                    "measurement_unit"      => $items['MeasurmentUnit']?$items['MeasurmentUnit']:null,
                    "is_invoice_created"    => true,
                    "is_shipment_added"     => true,
                    'status'                => Order::STATUS_ORDER,
                    'order_date'            => now(), 
                    "tax_modality"          => in_array($this->getValue("AE{$row}"),['ddp','ddu',"DDP",'DDU'])?strtoupper($this->getValue("AE{$row}")):'DDU',
                    "sender_first_name"     => $items['SenderFirstName']?$items['SenderFirstName']:null,
                    "sender_last_name"      => $items['SenderLastName']?$items['SenderLastName']:null,
                    "sender_email"          => $items['SenderEmail']?$items['SenderEmail']:null,
                    "sender_phone"          => $items['SenderPhone']?$items['SenderPhone']:null,
                    'user_declared_freight' => $items['FreightToCustom']?$items['FreightToCustom']:null,
                    'error'                 => $orderError,
                    'recipient' => [
                        "first_name"        => $items['RecipientFirstName']?$items['RecipientFirstName']:null,
                        "last_name"         => $items['RecipientLastName']?$items['RecipientLastName']:null,
                        "email"             => $items['RecipientEmail']?$items['RecipientEmail']:null,
                        "phone"             => $items['RecipientPhone']?$items['RecipientPhone']:null,
                        "address"           => $items['RecipientAddress']?$items['RecipientAddress']:null,
                        "address2"          => $items['RecipientAddress2']?$items['RecipientAddress2']:null,
                        "street_no"         => $items['RecipientHouseNo']?$items['RecipientHouseNo']:null,
                        "zipcode"           => $items['RecipientZipcode']?$items['RecipientZipcode']:null,
                        "city"              => $items['RecipientCity']?$items['RecipientCity']:null,
                        "account_type"      => 'individual',
                        "state_id"          => optional( State::where('code',$items['RecipientStateAbbrivation']?$items['RecipientStateAbbrivation']:null)->first() )->id,
                        "country_id"        => optional( Country::where('code',$items['RecipientCountryCodeIso']?$items['RecipientCountryCodeIso']:null)->first() )->id,
                        "tax_id"            => $items['RecipientTaxId']?$items['RecipientTaxId']:null,
                    ],

                ]);
                
                if(isset($items['OrderItem']['ProductQuantity'])){
                    $order = $this->addItem($order,$items['OrderItem']);
                }else{
                    foreach($items['OrderItem'] as $itemValue){
                        $order = $this->addItem($order,$itemValue);
                    }
                }
                
                // $order->doCalculations();
                DB::commit();
                return $order;
            
            
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::info($ex);
            \Log::info("{xml}: ".$ex->getMessage());
        }
    }

    public function addItem($order,$orderItems)
    {
        $this->validationRow($orderItems, true);
        
        $item =[
            "quantity"          => $orderItems['ProductQuantity']?$orderItems['ProductQuantity']:null,
            "value"             => $orderItems['ProductValue']?$orderItems['ProductValue']:null,
            "description"       => $orderItems['ProductDescription']?$orderItems['ProductDescription']:null,
            "sh_code"           => $orderItems['NCM']?$orderItems['NCM']:null,
            "contains_perfume"  => $orderItems['Perfume']? true : false,
            "contains_battery"  => $orderItems['Battery']? true : false,
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
            'upload_path' => $this->filePath,
        ]);

        return $importOrder;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function rules( $isItem, $items): array
    {
        if($isItem == true){
            return [
                'quantity' => 'required',
                'value' => 'required',
                'description' => 'required',
                'sh_code' => 'required',
            ];
        }

        if($isItem == false){
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
                    'required','max:15','min:13', new PhoneNumberValidator(optional( Country::where('code',$items['RecipientCountryCodeIso']?$items['RecipientCountryCodeIso']:null)->first() )->id)
                ],
                'zipcode' => [
                    'required', new ZipCodeValidator(optional( Country::where('code',$items['RecipientCountryCodeIso']?$items['RecipientCountryCodeIso']:null)->first() )->id,optional( State::where('code',$items['RecipientStateAbbrivation']?$items['RecipientStateAbbrivation']:null)->first() )->id)
                ],

            ];

            if (Country::where('code', 'BR')->first()->id == optional( Country::where('code',$items['RecipientCountryCodeIso']?$items['RecipientCountryCodeIso']:null)->first() )->id ) {
                $rules['recipient_tax_id'] = 'required';
            }

            return $rules;

        }
    }

    public function validationMessages( $isItem)
    {
        if($isItem == true){
            
            return [
                'quantity.required' => 'quantity is required',
                'value.required' => 'value is required',
                'description.required' => 'Product name description required',
                'sh_code.required' => 'NCM sh code is required',
            ];
        }

        if($isItem == false){
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
                'phone.min' => 'The phone may not be less than 13 characters.',
                'phone.max' => 'The phone may not be greater than 15 characters.',
                'phone.*.required' => 'Number should be in Brazil International Format',
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
    
    public function validationData($isItem, $items)
    {
        
        if($isItem == true){
            
            return [
                "quantity"      => $items['ProductQuantity']?$items['ProductQuantity']:null,
                "value"         => $items['ProductValue']?$items['ProductValue']:null,
                "description"   => $items['ProductDescription']?$items['ProductDescription']:null,
                "sh_code"       => $items['NCM']?$items['NCM']:null,
            ];
        }

        if($isItem == false){
            return [
                "merchant"          => $items['Merchant']?$items['Merchant']:null,
                "carrier"           => $items['Carrier']?$items['Carrier']:null,
                "tracking_id"       => $items['TrackingId']?$items['TrackingId']:null,
                "customer_reference"=> $items['CustomerRefrence']?$items['CustomerRefrence']:null,
                "height"            => $items['Height']?$items['Height']:null,
                "weight"            => $items['Weight']?$items['Weight']:null,
                "length"            => $items['Length']?$items['Length']:null,
                "width"             => $items['Width']?$items['Width']:null,
                "measurement_unit"  => $items['MeasurmentUnit']?$items['MeasurmentUnit']:null,

                "sender_first_name" => $items['SenderFirstName']?$items['SenderFirstName']:null,
                "sender_last_name"  => $items['SenderLastName']?$items['SenderLastName']:null,
                "sender_email"      => $items['SenderEmail']?$items['SenderEmail']:null,
                "sender_phone"      => $items['SenderPhone']?$items['SenderPhone']:null,

                "first_name"    => $items['RecipientFirstName']?$items['RecipientFirstName']:null,
                "last_name"     => $items['RecipientLastName']?$items['RecipientLastName']:null,
                "email"         => $items['RecipientEmail']?$items['RecipientEmail']:null,
                "phone"         => $items['RecipientPhone']?$items['RecipientPhone']:null,
                "address"       => $items['RecipientAddress']?$items['RecipientAddress']:null,
                "address2"      => $items['RecipientAddress2']?$items['RecipientAddress2']:null,
                "street_no"     => $items['RecipientHouseNo']?$items['RecipientHouseNo']:null,
                "zipcode"       => $items['RecipientZipcode']?$items['RecipientZipcode']:null,
                "city"          => $items['RecipientCity']?$items['RecipientCity']:null,

                
                "state_id" => optional( State::where('code', $items['RecipientStateAbbrivation']?$items['RecipientStateAbbrivation']:null )->first() )->id,
                "country_id" => optional( Country::where('code',$items['RecipientCountryCodeIso']?$items['RecipientCountryCodeIso']:null )->first() )->id,
                "recipient_tax_id" => $items['RecipientTaxId']?$items['RecipientTaxId']:null,
            ];
        }

    }

    public function validationRow($items, $isItem)
    {
        $validator = Validator::make($this->validationData($isItem, $items), $this->rules($isItem, $items), $this->validationMessages($isItem));
        
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

    public function getStoragePath($filename)
    {
        return storage_path("app/xml/{$filename}");
    }

    public function importFile(UploadedFile $file)
    {
        $fiename = md5(microtime()).'.'.$file->getClientOriginalExtension();
        $file->storeAs("xml/", $fiename);
        return $fiename;
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
