<?php

namespace App\Http\Livewire\Order;

use App\Models\State;
use App\Models\Address;
use App\Models\Country;
use Livewire\Component;
use App\Facades\USPSFacade;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Repositories\DomesticLabelRepository;

class UsLabelForm extends Component
{
    public $order;
    public $userId;
    public $states;
    public $usShippingServices;
    public $usServicesErrors;
    public $upsError;
    public $uspsError;
    public $fedexError;
    public $hasRates = false;
    public $usRates = [];

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

    protected $listeners = [
        'searchedAddress' => 'searchAddress',
        'phoneNumber' => 'enteredPhoneNumber',
        'saveAddress' => 'saveAddress',
    ];

    protected $rules = [
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

    public function mount($order, $states, $usShippingServices, $errors)
    {
        $this->order = $order;
        $this->states = $states;
        $this->usShippingServices = $usShippingServices;
        $this->usServicesErrors = $errors;

        if ($this->order) {
            $this->senderPhone = $this->order->user->phone;
            $this->userId = $this->order->user_id;
        }
    }

    public function render()
    {
        return view('livewire.order.us-label-form');
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

        $response = $this->callForUSPSAddressApi($request);
        
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

    private function callForUSPSAddressApi($request)
    {
        return USPSFacade::validateAddress($request);
    }

    public function getRates(DomesticLabelRepository $domesticLabelRepostory)
    {
        $this->validate();
        $this->usRates = [];
        
        if ($this->usShippingServices)
        {
            $domesticLabelRepostory->handle();
            $this->createRequest();
            $this->usRates = $domesticLabelRepostory->getRatesForDomesticServices($this->usShippingServices);
            
            $this->uspsError = $domesticLabelRepostory->getError();
            $this->upsError = $domesticLabelRepostory->getError();
            $this->fedexError = $domesticLabelRepostory->getError();

            $this->excludeShippingServices();
        }    
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
        $this->validate();

        if (!$this->selectedService) {
            return $this->addError('selectedService', 'select service please.');
        }

        $this->getCostOfSelectedService();
        $this->createRequest();
        request()->merge([
            'service' => $this->selectedService,
            'total_price' => $this->selectedServiceCost,
        ]);

        $domesticLabelRepostory->handle();
        if($domesticLabelRepostory->getDomesticLabel( $this->order))
        {
            $this->saveAddress();
            return redirect()->route('admin.order.us-label.index', $this->order->id);
        }

        $this->uspsError = $domesticLabelRepostory->getError();
        $this->upsError = $domesticLabelRepostory->getError();
        $this->fedexError = $domesticLabelRepostory->getError();
    }

    private function createRequest()
    {
        request()->merge([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'sender_state' => $this->senderState,
            'sender_address' => $this->senderAddress,
            'sender_city' => $this->senderCity,
            'sender_zipcode' => $this->senderZipCode,
            'sender_phone' => $this->senderPhone,
            'order_id' => $this->order->id,
            'pickupShipment' => ($this->pickupType == 'true') ? true : false,
            'pickup_date' => $this->pickupDate,
            'earliest_pickup_time' => $this->earliestPickupTime,
            'latest_pickup_time' => $this->latestPickupTime,
            'pickup_location' => $this->pickupLocation,
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
        }

        return;
    }
}
