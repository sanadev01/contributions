<?php

namespace App\Http\Livewire\Order;

use Exception;
use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Repositories\FedExLabelRepository;
use Illuminate\Support\Facades\Http;
use App\Repositories\UPSLabelRepository;
use App\Repositories\USPSLabelRepository;

class UsLabelForm extends Component
{
    public $order;
    public $states;
    public $usShippingServices;
    public $usServicesErrors;
    public $upsError;
    public $uspsError;
    public $hasRates = false;
    public $usRates = [];
    public $api_url;
    public $email;
    public $password;

    public $selectedService;
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

    public $selectedServiceCost;
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
        $this->usServicesErrors = $errors;

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

    public function getRates(UPSLabelRepository $upsLabelRepository, USPSLabelRepository $uspsLabelRepository, FedExLabelRepository $fedExLabelRepository)
    {
        $this->validate();
        $this->usRates = [];
        
        if ($this->usShippingServices)
        {
            $this->usShippingServices->each(function ($shippingService, $key) use ($upsLabelRepository, $uspsLabelRepository, $fedExLabelRepository) {
                if ($shippingService['service_sub_class'] == ShippingService::UPS_GROUND) {
                    $this->getUPSRates($shippingService['service_sub_class'], $upsLabelRepository);
                }

                if ($shippingService['service_sub_class'] == ShippingService::USPS_PRIORITY
                    || $shippingService['service_sub_class'] == ShippingService::USPS_FIRSTCLASS) 
                {
                    $this->getUSPSRates($shippingService['service_sub_class'], $uspsLabelRepository);
                }

                if ($shippingService['service_sub_class'] == ShippingService::FEDEX_GROUND) {
                   $this->getFedexRates($shippingService['service_sub_class'], $fedExLabelRepository);
                }
            });
        }    
    }

    public function getLabel(UPSLabelRepository $upsLabelRepository, USPSLabelRepository $uspsLabelRepository)
    {
        $this->validate();

        if (!$this->selectedService) {
            return $this->addError('selectedService', 'select service please.');
        }

        $this->getCostOfSelectedService();
        if ($this->selectedService == ShippingService::UPS_GROUND) 
        {
            return $this->getUPSLabel($upsLabelRepository);
        }

        $this->getUSPSLabel($uspsLabelRepository);
        
    }

    private function getUPSRates($service, $upsLabelRepository)
    {
        $request = $this->createRequest($service);

        $request->merge(['pickup' => $this->pickupType]);

        $upsRateResponse = $upsLabelRepository->getRates($request);
        if ($upsRateResponse['success'] == true) {
          return array_push($this->usRates, ['service' => 'UPS Ground', 'service_code' => $service, 'cost' => $upsRateResponse['total_amount']]);
        }

        $this->upsError = $upsRateResponse['message'];
    }

    private function getUSPSRates($service, $uspsLabelRepository)
    {
        $uspsRateResponse = $uspsLabelRepository->getRates($this->createRequest($service));
        if ($uspsRateResponse['success'] == true) {
            return array_push($this->usRates, ['service' => ($service == ShippingService::USPS_PRIORITY) ? 'USPS Priority' : 'USPS FirstClass', 'service_code' => $service, 'cost' => $uspsRateResponse['total_amount']]);
        }

        $this->uspsError = $uspsRateResponse['message'];
    }

    private function getFedexRates($service, $fedExLabelRepository)
    {
        $fedExRateResponse = $fedExLabelRepository->getRates($this->createRequest($service));
        if ($fedExRateResponse['success'] == true) {
            return array_push($this->usRates, ['service' => 'FedEx Ground', 'service_code' => $service, 'cost' => $fedExRateResponse['total_amount']]);
        }

        $this->fedExError = $fedExRateResponse['message'];
    }

    private function getUPSLabel($upsLabelRepository)
    {
        $request = $this->createRequest($this->selectedService);
        $request->merge(['total_price' => $this->selectedServiceCost]);

        $request = ($this->pickupType == true) ? $request->merge(['pickup' => $this->pickupType]) : $request;
        
        $upsLabelRepository->buyLabel($request, $this->order);

        $this->upsError = $upsLabelRepository->getUPSErrors();
        
        if (!$this->upsError) {
            return redirect()->route('admin.order.us-label.index', $this->order->id);
        }

        return false;
    }

    private function getUSPSLabel($uspsLabelRepository)
    {
        $request = $this->createRequest($this->selectedService);
        $request->merge(['total_price' => $this->selectedServiceCost]);
        
        $uspsLabelRepository->buyLabel($request, $this->order);

        $this->uspsError = $uspsLabelRepository->getUSPSErrors();

        if ($this->uspsError == null) {
            return redirect()->route('admin.order.us-label.index', $this->order->id);
        }

        return false;
    }

    private function createRequest($servcie)
    {
       return new Request([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'sender_state' => $this->senderState,
            'sender_address' => $this->senderAddress,
            'sender_city' => $this->senderCity,
            'sender_zipcode' => $this->senderZipCode,
            'service' => $servcie,
            'order_id' => $this->order->id,
            'pickup_date' => $this->pickupDate,
            'earliest_pickup_time' => $this->earliestPickupTime,
            'latest_pickup_time' => $this->latestPickupTime,
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
}
