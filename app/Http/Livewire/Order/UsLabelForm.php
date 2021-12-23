<?php

namespace App\Http\Livewire\Order;

use Exception;
use Livewire\Component;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\Http;
use App\Repositories\UPSLabelRepository;

class UsLabelForm extends Component
{
    public $order;
    public $states;
    public $usShippingServices;
    private $upsLabelRepository;
    public $errors;
    public $hasRates = false;
    public $api_url;
    public $email;
    public $password;

    public $firstName;
    public $lastName;
    public $senderState;
    public $senderAddress;
    public $senderCity;
    public $senderZipCode;
    public $pickupType = false;
    public $pickupDate;
    public $earliestPickupTime;
    public $latestPickupTime;
    public $pickupLocation;
    public $service;

    public $zipCodeResponse;
    public $zipCodeResponseMessage;
    public $zipCodeClass;

    protected $rules = [
        'firstName' => 'required',
        'lastName' => 'required',
        'senderState' => 'required',
        'senderAddress' => 'required',
        'senderCity' => 'required',
        'senderZipCode' => 'required',
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
        $this->errors = $errors;

        $this->setUSPSAddressApiCredentials();
    }

    public function render()
    {
        return view('livewire.order.us-label-form');
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

    private function validateUSAddress()
    {
        $this->validate([
            'senderState' => 'required',
            'senderAddress' => 'required|min:4',
            'senderCity' => 'required|min:4',
        ]);

        $this->callForUSPSAddressApi();
    }

    private function callForUSPSAddressApi()
    {
        $data = $this->makeRequestBodyForAddressValidation();

        try {

            $response = Http::withBasicAuth($this->email, $this->password)->post($this->api_url, $data);
            
            if($response->status() == 200) {
                $this->senderZipCode = $response->json()['zip5'];
                $this->zipCodeResponse = true;
                $this->zipCodeResponseMessage = 'according to your given address your zipcode is: '.$this->senderZipCode;
                $this->zipCodeClass = 'text-success';
            }

            if($response->status() != 200) {
                $this->zipCodeResponse = true;
                $this->zipCodeResponseMessage = $response->json()['message'];
                $this->zipCodeClass = 'text-danger';
            }
        } catch (Exception $e) {
            $this->zipCodeResponse = true;
            $this->zipCodeResponseMessage = $e->getMessage();
            $this->zipCodeClass = 'text-danger';
        }
    }

    private function makeRequestBodyForAddressValidation()
    {
        $data = [
            'company_name' => 'Herco',
            'line1' => $this->senderAddress,
            'state_province' => $this->senderState,
            'city' => $this->senderCity,
            'postal_code' => '',
            'country_code' => 'US'
        ];

        return $data;
    }

    private function setUSPSAddressApiCredentials()
    {
        $this->api_url = 'https://api.myibservices.com/v1/address/validate';
        $this->email = config('usps.email');           
        $this->password = config('usps.password');
    }

    public function getRates(UPSLabelRepository $upsLabelRepository)
    {
        $this->validate();
        
        $this->usShippingServices->each(function ($shippingService, $key) use ($upsLabelRepository) {
            if ($shippingService['service_sub_class'] == ShippingService::UPS_GROUND) {
                $this->getUPSRates($shippingService['service_sub_class'], $upsLabelRepository);
            }
        });
    }

    private function getUPSRates($service, $upsLabelRepository)
    {
        $upsRateResponse = $upsLabelRepository->getRates($this->createRequestBodyForUPS($service));
    }

    private function createRequestBodyForUPS($servcie)
    {
        return new Request([
            'first_name' => $this->firstName,
            'sender_state' => $this->senderState,
            'sender_address' => $this->senderAddress,
            'sender_city' => $this->senderCity,
            'sender_zipcode' => $this->senderZipCode,
            'service' => $servcie,
            'order_id' => $this->order->id,
            'pickup' => $this->pickupType,
        ]);
    }
}
