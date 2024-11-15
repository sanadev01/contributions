<?php

namespace App\Http\Livewire\AmazonOrders;

use App\Models\Order;
use App\Models\State;
use App\Models\ShCode;
use App\Models\Country;
use Livewire\Component;
use App\Models\ProfitPackage;
use App\Models\ShippingService;
use App\Rules\ZipCodeValidator;
use Illuminate\Support\Facades\DB;
use App\Rules\PhoneNumberValidator;
use Illuminate\Support\Facades\Auth;
use App\Repositories\OrderRepository;
use App\Services\Converters\UnitsConverter;
use App\Services\Calculators\WeightCalculator;

class CreateHdOrder extends Component
{
    public $orderId; 
    public $order;
    public $amazonOrder;
    public $sellerOrder;
    public $edit;

    public $merchant;
    public $carrier;
    public $tracking_id;
    public $customer_reference;
    public $correios_tracking_code;
    public $order_date;
    public $whr_number;
    public $weight;
    public $unit;
    public $length;
    public $width;
    public $height;

    public $weightOther;
    public $lengthOther;
    public $widthOther;
    public $heightOther;
    public $volumeWeight = 0;
    public $currentWeightUnit;

    public $sender_first_name;
    public $sender_last_name;
    public $sender_email;
    public $sender_phone;
    public $sender_taxId;
    public $sender_website;

    public $recipient;

    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $address;
    public $address2;
    public $street_no;
    public $country_id;
    public $state_id;
    public $city;
    public $zipcode;
    public $tax_id;

    public $items = []; 
    public $shCodes = [];
    public $user_declared_freight;
    public $states = [];
    public $shipping_service;
    public $shippingServices = [];

    private $orderRepository;

    public function mount($amazonOrder, $edit= '', OrderRepository $orderRepository)
    {
        $this->sellerOrder = $amazonOrder;
        $this->edit = $edit;
        $this->amazonOrder = $amazonOrder;
        $this->orderRepository = $orderRepository;

        $this->items = $amazonOrder;   
        $this->shCodes = ShCode::all()->toArray();

        foreach ($this->amazonOrder as $key => $item) {
            $this->items[$key] = [
                'sh_code' => null,
                'description' => $item->title,
                'quantity' => $item->number_of_items,
                'total' => number_format($item->number_of_items * $item->item_price, 2),
                'value' => $item->item_price,
                'contains_battery' => 0,
                'contains_perfume' => 0,
                'sale_order_id' => $item->sale_order_id,
            ];
        }

        $this->shippingServices = $this->getFilteredShippingServices();

        $this->fillData();
    }

    public function render()
    {
        return view('livewire.amazon-orders.create-hd-order');
    }


    public function save()
    {
        $rules = [
            'merchant' => 'required',
            'carrier' => 'required',
            'tracking_id' => 'required',
            'customer_reference' => 'nullable',
            'correios_tracking_code' => 'nullable',
            'order_date' => 'required',
            'weight' => 'required|numeric|min:0.1',
            'unit' => 'required',
            'length' => 'required|numeric|min:0.1',
            'width' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',

            'sender_first_name' => 'required|max:100',
            'sender_last_name' => 'max:100',
            'sender_email' => 'nullable|max:100|email',
            'sender_phone' => [
                'nullable','max:15','min:13'
            ],

            'first_name' => 'required|max:50',
            'last_name' => 'nullable|max:50',
            'address' => 'required',
            'address2' => 'nullable|max:50',
            'street_no' => 'required',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required',
            'phone' => [
                'required','min:13','max:15', new PhoneNumberValidator($this->country_id)
            ],
            'state_id' => 'required|exists:states,id',
            'zipcode' => [
                'required', new ZipCodeValidator($this->country_id,$this->state_id)
            ],

            'customer_reference' => 'nullable',
            'user_declared_freight' => 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/',

        ];

        if (Country::where('code', 'BR')->first()->id == $this->country_id) {
            $rules['tax_id'] = 'required';
        }

        $customMessages = [
            'merchant.required' => 'merchant is required',
            'carrier.required' => 'carrier is required',
            'tracking_id.required' => 'tracking id is required',
            'customer_reference.nullable' => 'customer reference is required',
            'correios_tracking_code.nullable' => 'correios tracking code required',
            'measurement_unit.required' => 'measurement unit is required',
            'weight.required' => 'weight is required',
            'length.required' => 'length is required',
            'width.required' => 'width is required',
            'height.required' => 'height is required',

            'sender_first_name.required' => 'sender first name is required',
            'sender_last_name.nullable' => 'sender last name is required',
            'sender_email.nullable' => 'sender Email is required',
            'sender_phone.nullable' => 'sender phone is required',


            'first_name.required' => 'first Name is required',
            'last_name.required' => 'last Name is required',
            'email.nullable' => 'email is not valid',
            'phone.required' => 'phone is required',
            'phone.min' => 'The phone must be at least 13 characters.',
            'phone.*.required' => 'Number should be in Brazil International Format',
            'phone.max' => 'The phone may not be greater than 15 characters.',
            'address.required' => 'address is required',
            'address2.nullable' => 'Address2 is not more then 50 character',
            'address2.*.nullable' => 'The address2 may not be greater than 50 characters.',
            'street_no.required' => 'house street no is required',
            'city.required' => 'city is required',
            'state_id.required' => 'state id is required',
            'country_id.required' => 'country is required',
            'zipcode.required' => 'zipcode is required',
            'tax_id.required' => 'The selected recipient tax id is invalid.',
            'tax_id.*.required' => 'The recipient tax id field is required.',

            'quantity.required' => 'quantity is required',
            'value.required' => 'value is required',
            'description.required' => 'Product name description required',
            'sh_code.required' => 'NCM sh code is required',
        ];
    
        $data = $this->validate($rules, $customMessages);

        DB::beginTransaction();

        try {

            $this->order = Order::create([
                'shipping_service_id' => $this->shipping_service,
                'user_id' => Auth::id(),
                "merchant" => $this->merchant,
                "carrier" => $this->carrier,
                "tracking_id" => $this->tracking_id,
                "customer_reference" => $this->customer_reference,
                "measurement_unit" => $this->unit,
                "weight" =>  round($this->weight, 2),
                "length" =>  round($this->length, 2),
                "width" =>   round($this->width, 2),
                "height" =>  round($this->height, 2),
                "is_invoice_created" => true,
                "tax_modality" => 'ddu',
                "order_date" => now(),
                "is_shipment_added" => true,
                'status' => Order::STATUS_ORDER,
                'user_declared_freight' => $this->user_declared_freight ?? 0,
                'sinerlog_tran_id' => 1,

                "sender_first_name" => $this->sender_first_name,
                "sender_last_name" => $this->sender_last_name,
                "sender_email" => $this->sender_email,
                "sender_taxId" => $this->sender_taxId,
                'sender_country_id' => Country::US,
                'sender_state_id' => "4622",
                'sender_city' => "Miami",
                'sender_address' => "2200 NW, 129th Ave – Suite # 100",
                'sender_phone' => $this->sender_phone,
                'sender_zipcode' => "33182",
                'sender_website' => $this->sender_website? $this->sender_website : NULL,

                'tax_modality' => setting('is_prc_user', null, Auth::id())? 'DDP' : 'DDU',
            ]);
            
            $this->order->recipient()->create([
                "first_name" => $this->first_name,
                "last_name" => $this->last_name,
                "email" => $this->email,
                "phone" => $this->phone,
                "city" => $this->city,
                "street_no" => $this->street_no,
                "address" => $this->address,
                "address2" => $this->address2,
                "account_type" => 'individual',
                "tax_id" => $this->tax_id,
                "zipcode" => $this->zipcode,
                "state_id" => $this->state_id,
                "country_id" => $this->country_id
            ]);

            $isBattery = false;
            $isPerfume = false;

            foreach ($this->items as $product) {
                if (optional($product)['contains_battery']) {
                    $isBattery = true;
                }
                if (optional($product)['contains_perfume']) {
                    $isPerfume = true;
                }

                $shCode = optional($product)['sh_code'];
                $shCode = getValidShCode($shCode, $this->order->shippingService);

                $this->order->items()->create([
                    "sh_code" => $shCode,
                    "description" => optional($product)['description'],
                    "quantity" => optional($product)['quantity'],
                    "value" => optional($product)['value'],
                    "contains_battery" => optional($product)['contains_battery'],
                    "contains_perfume" => optional($product)['contains_perfume'],
                    "contains_flammable_liquid" => 0,
                ]);
            }

            $orderValue = collect($this->items)->sum(function ($item) {
                return $item['value'] * $item['quantity'];
            });


            $this->order->update([
                'warehouse_number' => $this->order->getTempWhrNumber(true),
                "order_value" => $orderValue,
                'shipping_service_name' => $this->order->shippingService->name
            ]);

            $this->order->syncServices([]);

            $this->order->doCalculations();

            DB::table('sale_orders')
                ->where('id', $this->sellerOrder->first()['sale_order_id'])
                ->update(['seller_order_id' => $this->order->warehouse_number]);

            DB::commit();

            if ($this->order) {
                session()->flash('message', 'Order successfully created');
                return redirect()->route('amazon.orders');
            }
            
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::info("Amazon Order Create Exception: {$ex->getMessage()}");
        }
    }


    public function updatedUnit()
    {
        $this->calculateOtherUnits();
    }

    public function updatedWeight()
    {
        $this->calculateOtherUnits();
    }

    public function updatedLength()
    {
        $this->calculateOtherUnits();
    }

    public function updatedWidth()
    {
        $this->calculateOtherUnits();
    }

    public function updatedHeight()
    {
        $this->calculateOtherUnits();
    }

    private function fillData()
    {
        $this->calculateOtherUnits();
    }

    public function calculateOtherUnits()
    {
        $this->weight = $this->weight ? $this->weight : 0;
        $this->length = $this->length ? $this->length : 0;
        $this->width = $this->width ? $this->width : 0;
        $this->height = $this->height ? $this->height : 0;

        if ( $this->unit == 'kg/cm' ){
            $this->weightOther = UnitsConverter::kgToPound($this->weight);
            $this->lengthOther = UnitsConverter::cmToIn($this->length);
            $this->widthOther = UnitsConverter::cmToIn($this->width);
            $this->heightOther = UnitsConverter::cmToIn($this->height);
            $this->currentWeightUnit = 'kg';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length,$this->width,$this->height,'cm');
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight,2);
        }else{
            $this->weightOther = UnitsConverter::poundToKg($this->weight);
            $this->lengthOther = UnitsConverter::inToCm($this->length);
            $this->widthOther = UnitsConverter::inToCm($this->width);
            $this->heightOther = UnitsConverter::inToCm($this->height);
            $this->currentWeightUnit = 'lbs';
            $volumetricWeight = WeightCalculator::getVolumnWeight($this->length,$this->width,$this->height,'in');
            $this->volumeWeight = round($volumetricWeight > $this->weight ? $volumetricWeight : $this->weight,2);
        }
    }

    public function updatedCountryId($value)
    {
        $this->states = State::where('country_id', $value)->get();
    }

    private function getFilteredShippingServices()
    {
        $api = currentActiveApiName();
        $allowedSubClasses = [];

        switch ($api) {
            case 'Correios Anjun Api':
                $allowedSubClasses = [
                    ShippingService::AJ_Packet_Standard,
                    ShippingService::AJ_Packet_Express
                ];
                break;
            case 'Correios Api':
                $allowedSubClasses = [
                    ShippingService::Packet_Express,
                    ShippingService::Packet_Standard
                ];
                break;
            case 'BCN Setting':
                $allowedSubClasses = [
                    ShippingService::BCN_Packet_Standard,
                    ShippingService::BCN_Packet_Express
                ];
                break;
            default:
                break;
        }
        
        $availableServices = ShippingService::whereIn('service_sub_class', $allowedSubClasses)
            ->pluck('name', 'id')
            ->toArray();

        return $availableServices;
    }

    public function updatedShippingService()
    {
        $this->getFreightRate();
    }

    public function getFreightRate()
    {
        if (!$this->shipping_service) {
            return; 
        }

        $packageId = DB::table('profit_settings')
            ->where('user_id', Auth::user()->id)
            ->where('service_id', $this->shipping_service)
            ->value('package_id');

        if (!$packageId) {
            return;
        }

        $profitPacket = ProfitPackage::where('id', $packageId)->first();

        if (!$profitPacket || !is_array($profitPacket->data)) {
            return;
        }

        // \Log::info("Volume Weight: {$this->volumeWeight}");

        foreach ($profitPacket->data as $package) {
            $minWeight = $package['min_weight'];
            $maxWeight = $package['max_weight'];

            if ($this->volumeWeight >= $minWeight && $this->volumeWeight <= $maxWeight) {

                // $this->user_declared_freight = $package['value'];
                // \Log::info("Matched Rate: {$this->user_declared_freight}");
                return; 
            }
        }

        // $this->user_declared_freight = 0;
    }



}
