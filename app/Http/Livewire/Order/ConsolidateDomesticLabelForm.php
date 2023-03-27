<?php

namespace App\Http\Livewire\Order;

use App\Models\State;
use App\Models\Address;
use App\Models\Country;
use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Repositories\DomesticLabelRepository;

class ConsolidateDomesticLabelForm extends Component
{
    public $consolidatedOrder;
    public $orders;
    public $userId;
    public $states;
    public $usShippingServices;
    public $errors;

    public $upsError;
    public $uspsError;
    public $fedexError;
    public $hasRates = false;
    public $usRates = [];
    public $shippingSerivceErrors = [];

    public $selectedService;
    public $firstName;
    public $lastName;
    public $senderState;
    public $senderAddress;
    public $senderCity;
    public $senderZipCode;
    public $senderPhone;
    public $pickupType = false;
    public $pickupDate;
    public $earliestPickupTime;
    public $latestPickupTime;
    public $pickupLocation;
    public $service;

    public $selectedServiceCost;
    public $zipCodeResponse;
    public $zipCodeResponseMessage;
    public $zipCodeClass;

    public $weight;
    public $length;
    public $width;
    public $height;
    public $unit = 'lbs/in';
    public $volumeWeight;

    public $consolidationErrors;

    public $totalWeight;

    protected $tempOrder;

    protected $rules = [
        'weight' => 'required|numeric',
        'length' => 'required|numeric',
        'width' => 'required|numeric',
        'height' => 'required|numeric',
        'unit' => 'required',
        'volumeWeight' => 'required|numeric',
        'firstName' => 'required',
        'lastName' => 'required',
        'senderState' => 'required',
        'senderAddress' => 'required',
        'senderCity' => 'required',
        'senderZipCode' => 'required',
        'senderPhone' => 'required|max:12',
        'pickupType' => 'required',
        'pickupDate' => 'required_if:pickupType,true',
        'earliestPickupTime' => 'required_if:pickupType,true',
        'latestPickupTime' => 'required_if:pickupType,true',
        'pickupLocation' => 'required_if:pickupType,true',
    ];

    protected $listeners = [
        'updatedWeight',
        'updatedUnit',
        'updatedLength',
        'updatedWidth',
        'updatedHeight',
        'volumeWeight',
        'searchedAddress' => 'searchAddress',
        'phoneNumber' => 'enteredPhoneNumber',
    ];

    public function mount($orders, $states, $errors, $totalWeight)
    {
        $this->orders = $orders;
        $this->states = $states;
        $this->consolidationErrors = $errors;
        $this->totalWeight = $totalWeight;
        
        if ($this->orders->count() > 0) {
            $this->userId = $this->orders->first()->user_id;
        }
    }

    public function render()
    {
        return view('livewire.order.consolidate-domestic-label-form');
    }

    public function enteredPhoneNumber($value)
    {
        $this->senderPhone = $value;
    }

    public function searchAddress($address)
    {
        $this->senderState = State::find($address['state_id'])->code;
        $this->firstName = $address['first_name'];
        $this->lastName = $address['last_name'];
        $this->senderAddress = $address['address'];
        $this->senderCity = $address['city'];
        $this->senderZipCode = $address['zipcode'];
        $this->senderPhone = $address['phone'];
    }

    public function updatedWeight($weight)
    {
        $this->weight = $weight;
        $this->usRates = [];
    }

    public function updatedUnit($unit)
    {
        $this->unit = $unit;
        $this->usRates = [];
    }

    public function updatedLength($length)
    {
        $this->length = $length;
        $this->usRates = [];
    }

    public function updatedWidth($width)
    {
        $this->width = $width;
        $this->usRates = [];
    }

    public function updatedHeight($height)
    {
        $this->height = $height;
        $this->usRates = [];
    }

    public function volumeWeight($volumeWeight)
    {
        $this->volumeWeight = $volumeWeight;
        $this->usRates = [];
    }

    public function updatedsenderState()
    {
        $this->validateUSAddress();
    }

    public function updatedsenderAddress()
    {
        $this->validateUSAddress();
    }

    public function updatedsenderCity()
    {
        $this->validateUSAddress();
    }

    public function updatedsenderPhone()
    {
        $this->validate();
    }

    public function setAddress()
    {
        $this->saveAddress();
    }

    private function validateUSAddress()
    {
        $this->validate([
            'senderState' => 'required',
            'senderAddress' => 'required|min:4',
            'senderCity' => 'required|min:4',
        ]);

        $request = new Request([
            'state' => $this->senderState,
            'address' => $this->senderAddress,
            'city' => $this->senderCity,
        ]);

        $domesticLabelRepostory = new DomesticLabelRepository();
        $response = $domesticLabelRepostory->validateAddress($request);

        if ($response['success'] == true) {
            $this->senderZipCode = $response['zipcode'];
            $this->zipCodeResponse = true;
            $this->zipCodeResponseMessage = 'according to your given address your zipcode is: '.$this->senderZipCode;
            $this->zipCodeClass = 'text-success';
            return true;
        }

        $this->senderZipCode = '';
        $this->zipCodeResponse = true;
        $this->zipCodeResponseMessage = $response['message'];
        $this->zipCodeClass = 'text-danger';
    }

    public function getRates(DomesticLabelRepository $domesticLabelRepostory)
    {
        $this->validate();
        $this->usRates = [];
        $this->createRequest();

        $domesticLabelRepostory->handle();
        $this->tempOrder = $domesticLabelRepostory->getTempOrder(request());
        $this->usShippingServices = $domesticLabelRepostory->getShippingServices($this->tempOrder);
        
        if ($this->usShippingServices->isEmpty()) {
            $this->shippingSerivceErrors = $domesticLabelRepostory->getShippingServiceErrors();
            return false;
        }

        $this->shippingSerivceErrors = null;
        
        request()->merge([
            'order' => $this->tempOrder,
        ]);

        $this->usRates = $domesticLabelRepostory->getRatesForDomesticServices($this->usShippingServices);
        $this->fedexError = $domesticLabelRepostory->getError();
        
        $this->excludeShippingServices();
    }

    private function excludeShippingServices()
    {
        $this->usShippingServices = $this->usShippingServices->filter(function ($service) {
            foreach ($this->usRates as $rate) {
                if($rate['service_code'] == $service['service_sub_class'])
                {
                    return true;
                }
            }
        });
    }

    public function getLabel(DomesticLabelRepository $domesticLabelRepostory)
    {
        $this->uspsError = '';
        $this->upsError = '';

        $this->validate();

        if (!$this->selectedService) {
            return $this->addError('selectedService', 'select service please.');
        }

        $this->getCostOfSelectedService();
        $this->createRequest();

        $domesticLabelRepostory->handle();
        $this->tempOrder = $domesticLabelRepostory->getTempOrder(request());

        request()->merge([
            'service' => $this->selectedService,
            'total_price' => $this->selectedServiceCost,
            'orders' => $this->orders,
            'order' => $this->tempOrder,
        ]);


        if($domesticLabelRepostory->getDomesticLabel(request()->order))
        {
            $this->saveAddress();
            return redirect()->route('admin.order.us-label.index', $this->orders->first()->id);
        }

        $this->uspsError = $domesticLabelRepostory->getError();
        $this->upsError = $domesticLabelRepostory->getError();
    }

    private function createRequest()
    {
        request()->merge([
            'weight' => (int)$this->weight,
            'length' => (int)$this->length,
            'width' => (int)$this->width,
            'height' => (int)$this->height,
            'unit' => $this->unit,
            'volumeWeight' => $this->volumeWeight,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'sender_state' => $this->senderState,
            'sender_address' => $this->senderAddress,
            'sender_city' => $this->senderCity,
            'sender_zipcode' => $this->senderZipCode,
            'sender_phone' => $this->senderPhone,
            'user' => $this->orders->first()->user,
            'order' => $this->tempOrder,
            'pickupShipment' => ($this->pickupType == 'true') ? true : false,
            'pickup_date' => $this->pickupDate,
            'earliest_pickup_time' => $this->earliestPickupTime,
            'latest_pickup_time' => $this->latestPickupTime,
            'pickup_location' => $this->pickupLocation,
            'consolidated_order' => true,
        ]);
    }

    private function getCostOfSelectedService()
    {
        Arr::where($this->usRates, function ($value, $key) {
            if($value['service_code'] == $this->selectedService)
            {
                return $this->selectedServiceCost = $value['cost'];
            }
        });
    }

    private function saveAddress()
    {
        $existingAddress = Address::where([['user_id', $this->userId],['phone', $this->senderPhone]])->first();
        
        if (!$existingAddress) {
            Address::create([
                'user_id' => $this->userId,
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'phone' => $this->senderPhone,
                'address' => $this->senderAddress,
                'city' => $this->senderCity,
                'state_id' => State::where([['code', $this->senderState], ['country_id', Country::US]])->first()->id,
                'country_id' => Country::US,
                'zipcode' => $this->senderZipCode,
                'account_type' => 'individual',
            ]);
        } else {
            $existingAddress->update([
                'first_name' => $this->firstName,
                'last_name' => $this->lastName,
                'address' => $this->senderAddress,
                'city' => $this->senderCity,
                'state_id' => State::where([['code', $this->senderState], ['country_id', Country::US]])->first()->id,
                'zipcode' => $this->senderZipCode,
            ]);
        }

        return;
    }
}
