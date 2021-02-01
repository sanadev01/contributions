<?php

namespace App\Services\Excel\Import;

use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use App\Models\ImportOrder;
use Illuminate\Support\Arr;
use App\Models\ImportedOrder;
use App\Models\ShippingService;
use App\Rules\ZipCodeValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Rules\PhoneNumberValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\AbstractImportService;

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
        foreach($data as $items){
            $this->createOrUpdateOrder($items,$importOrder);
        }
    }

    private function createOrUpdateOrder($items, $importOrder)
    {

        try {
                unset($this->errors);
                $this->errors = array();

                $this->validationRow($items, false);
            
                DB::beginTransaction();
                $shippingService = ShippingService::first();
                
                $orderError = null;
                if(!empty($this->errors)){
                    $orderError = $this->errors;
                }
                
                $order = ImportedOrder::create([
                    'user_id' => $this->userId,
                    'import_id' => $importOrder->id,
                    'shipping_service_id' => $shippingService->id,
                    'shipping_service_name' => $shippingService->name,
                    "merchant" => null,
                    "carrier" => null,
                    "tracking_id" => null,
                    "customer_reference" => null,
                    "weight" => 0,
                    "length" => 0,
                    "width" => 0,
                    "height" => 0,
                    "measurement_unit" => 'kg/cm',
                    "is_invoice_created" => true,
                    "is_shipment_added" => true,
                    'status' => Order::STATUS_ORDER,
                    'order_date' => now(), 

                    "sender_first_name" => $items['Sender']['FirstName'],
                    "sender_last_name" => $items['Sender']['LastName'],
                    "sender_email" => $items['Sender']['OrderedEmailAddresses']['Address']?$items['Sender']['OrderedEmailAddresses']['Address']:null,
                    "sender_phone" => $items['Sender']['OrderedPhoneNumbers']['Number']?$items['Sender']['OrderedPhoneNumbers']['Number']:null,

                
                    'user_declared_freight' => 0,
                    'error' => $orderError,

                    'recipient' => [
                        "first_name" => $items['Recipient']['AddressFields']['FirstName']?$items['Recipient']['AddressFields']['FirstName']:null,
                        "last_name" => $items['Recipient']['AddressFields']['LastName']?$items['Recipient']['AddressFields']['LastName']:null,
                        "email" => $items['Recipient']['AddressFields']['OrderedEmailAddresses']['Address']?$items['Recipient']['AddressFields']['OrderedEmailAddresses']['Address']:null,
                        "phone" => $items['Recipient']['AddressFields']['OrderedPhoneNumbers']['Number']?$items['Recipient']['AddressFields']['OrderedPhoneNumbers']['Number']:null,
                        "address" => $items['Recipient']['AddressFields']['MultilineAddress']['Line'][0]?$items['Recipient']['AddressFields']['MultilineAddress']['Line'][0]:null,
                        "address2" => $items['Recipient']['AddressFields']['MultilineAddress']['Line'][1]?$items['Recipient']['AddressFields']['MultilineAddress']['Line'][1]:null,
                        "street_no" => $items['Recipient']['AddressFields']['MultilineAddress']['Line'][2]?$items['Recipient']['AddressFields']['MultilineAddress']['Line'][2]:null,
                        "zipcode" => $items['Recipient']['AddressFields']['PostalCode']?$items['Recipient']['AddressFields']['PostalCode']:null,
                        "city" => $items['Recipient']['AddressFields']['City']?$items['Recipient']['AddressFields']['City']:null,
                        "account_type" => 'individual',
                        "state_id" => null,
                        "country_id" => optional( Country::where('code',$items['Recipient']['AddressFields']['Country'])->first() )->id,
                        "tax_id" => '',
                    ],

                ]);
                
                $order = $this->addItem($order,optional($items['CustomsInfo']['Contents'])['Item']);
                
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
        foreach($orderItems as $value){
            $this->validationRow($value, true);
            
            $item =[
                "quantity" => $value['Quantity']?$value['Quantity']:null,
                "value" => $value['Value']?$value['Value']:null,
                "description" => $value['Description']?$value['Description']:null,
                "sh_code" => null,
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
        }    
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
                'customer_reference' => 'required',
                'weight' => 'required|numeric|gt:0',
                'measurement_unit' => 'required|in:kg/cm,lbs/in',
                'length' => 'required|numeric|gt:0',
                'width' => 'required|numeric|gt:0',
                'height' => 'required|numeric|gt:0',

                'sender_first_name' => 'required',
                'sender_last_name' => 'nullable',
                'sender_email' => 'required',
                'sender_phone' => [
                    'required','max:15','min:13', new PhoneNumberValidator(optional( Country::where('code',$items['Recipient']['AddressFields']['Country'])->first() )->id)
                ],
                
                
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
                    'required','max:15','min:13', new PhoneNumberValidator(optional( Country::where('code',$items['Recipient']['AddressFields']['Country'])->first() )->id)
                ],
                'zipcode' => [
                    'required', new ZipCodeValidator(optional( Country::where('code',$items['Recipient']['AddressFields']['Country'])->first() )->id,optional( State::where('code',$items['Recipient']['AddressFields']['PostalCode'])->first() )->id)
                ],

            ];

            if (Country::where('code', 'BR')->first()->id == optional( Country::where('code',$items['Recipient']['AddressFields']['Country'])->first() )->id ) {
                $rules['recipient_tax_id'] = ['required', "in:cpf,cnpj,CPF,CNPJ"];
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
                'customer_reference.required' => 'customer reference is required',
                'measurement_unit.required' => 'measurement unit is required',
                'weight.required' => 'weight is required',
                'length.required' => 'length is required',
                'width.required' => 'width is required',
                'height.required' => 'height is required',

                'sender_first_name.required' => 'sender first name is required',
                'sender_last_name.nullable' => 'sender last name is required',
                'sender_email.required' => 'sender Email is required',
                'sender_phone.required' => 'sender phone is required',
                
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
    
    public function validationData($isItem, $items)
    {
        if($isItem == true){
            
            return [
                "quantity" => $items['Quantity'],
                "value" => $items['Value'],
                "description" => $items['Description'],
                "sh_code" => null,
            ];
        }

        if($isItem == false){
            return [
                "merchant" => null,
                "carrier" => null,
                "tracking_id" => null,
                "customer_reference" => null,
                "measurement_unit" => 'kg/cm',
                "weight" => 0,
                "length" => 0,
                "width" => 0,
                "height" => 0,

                "sender_first_name" => $items['Sender']['FirstName'],
                "sender_last_name" => $items['Sender']['LastName'],
                "sender_email" => $items['Sender']['OrderedEmailAddresses']['Address'],
                "sender_phone" => $items['Sender']['OrderedPhoneNumbers']['Number'],

                "first_name" =>$items['Recipient']['AddressFields']['FirstName'],
                "last_name" => $items['Recipient']['AddressFields']['LastName'],
                "email" => $items['Recipient']['AddressFields']['OrderedEmailAddresses']['Address'],
                "phone" => $items['Recipient']['AddressFields']['OrderedPhoneNumbers']['Number'],
                "address" => $items['Recipient']['AddressFields']['MultilineAddress']['Line'][0],
                "address2" => $items['Recipient']['AddressFields']['MultilineAddress']['Line'][1],
                "street_no" => $items['Recipient']['AddressFields']['MultilineAddress']['Line'][2],
                "zipcode" => $items['Recipient']['AddressFields']['PostalCode'],
                "city" => $items['Recipient']['AddressFields']['City'],

                
                "state_id" => null,
                "country_id" => null,
                "zipcode" => null,
                "recipient_tax_id" => null,
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
}
