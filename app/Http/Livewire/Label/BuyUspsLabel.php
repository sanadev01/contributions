<?php

namespace App\Http\Livewire\Label;

use App\Models\Order;
use App\Models\State;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Repositories\USPSLabelRepository;
use App\Repositories\USPSBulkLabelRepository;

class BuyUspsLabel extends Component
{
    public $start_date;
    public $end_date;
    public $searchOrders;
    public $selectedOrders = [];
    public $states;
    public $shippingServices;
    public $firstName;
    public $lastName;
    public $selectedState;
    public $senderAddress;
    public $senderCity;
    public $senderZipCode;
    public $selectedService;
    public $order;
    public $updated = false;
    public $zipcodeResponse;
    public $reposnseClass;
    public $uspsRate;
    public $uspsRateError;
    public $totalWeight;

    public function render()
    {
        $this->getStates();
        return view('livewire.label.buy-usps-label');
    }

    public function search()
    {
        if($this->start_date != null || $this->end_date != null)
        {
            $orders = Order::where([
                                    ['user_id', auth()->user()->id],
                                    ['corrios_tracking_code', '!=', null],
                                    ['corrios_usps_tracking_code', null], 
                                ])->whereBetween('order_date',[$this->start_date.' 00:00:00', $this->end_date.' 23:59:59'])->get();
            $this->searchOrders = $orders;
        }
    }

    public function buyLabel()
    {
        $usps_labelRepository = new USPSBulkLabelRepository();
        $this->order = $usps_labelRepository->handle($this->selectedOrders);
        $this->totalWeight = $this->order->weight;
        $this->getShippingServices();
    }

    public function getStates()
    {
        $this->states = State::query()->where("country_id", 250)->get(["name","code","id"]);
    }

    public function getShippingServices()
    {
        $usps_labelRepository = new USPSLabelRepository();
        $shippingServices = $usps_labelRepository->getShippingServices($this->order);
        $this->shippingServices = $shippingServices->toArray();
        $this->firstName = (auth()->user()->name) ? auth()->user()->name : '';
        $this->lastName = (auth()->user()->last_name) ? auth()->user()->last_name : '';
    }

    protected $rules = [
        'firstName' => 'required',
        'lastName' => 'required',
        'selectedState' => 'required',
        'senderAddress' => 'required',
        'senderCity' => 'required',
    ];

    public function updatedselectedState()
    {
        $this->validate();
        $this->validateUSAddress();
    }

    public function updatedsenderAddress()
    {
        $this->validate();
        $this->validateUSAddress();
    }

    public function updatedsenderCity()
    {
        $this->validate();
        $this->validateUSAddress();
    }

    public function updatedselectedService()
    {
        $this->validate();
        $usps_labelRepository = new USPSBulkLabelRepository();
        $this->order = $usps_labelRepository->handle($this->selectedOrders);
        $this->getUSPSRates();
    }

    public function closeModal()
    {
        $this->resetFileds();
    }

    private function resetFileds()
    {
        $this->shippingServices = null;
        $this->firstName = null;
        $this->lastName = null;
        $this->selectedState = null;
        $this->senderAddress = null;
        $this->senderCity = null;
        $this->senderZipCode = null;
        $this->selectedService = null;
        $this->order = null;
        $this->zipcodeResponse = null;
        $this->reposnseClass = null;
        $this->uspsRate = null;
        $this->uspsRateError = null;
        $this->totalWeight = null;
    }

    private function validateUSAddress()
    {
        $url = route('api.orders.recipient.us_address');
        $response = Http::get($url, [
            'address' => $this->senderAddress,
            'state' => $this->selectedState,
            'city' => $this->senderCity,
        ]);
        
        $response = $response->json();
        if($response != null && $response['success'] == true)
        {
            $this->senderZipCode = $response['zipcode'];
            $this->reposnseClass = 'text-primary';
            $this->zipcodeResponse = 'According to your given Addrees, your zip code should be : '.$this->senderZipCode;
        } else {

            $this->reposnseClass = 'text-danger';
            $this->zipcodeResponse = $response['message'];
            $this->senderZipCode = null;
        }
    }

    private function getUSPSRates()
    {
        $usps_labelRepository = new USPSBulkLabelRepository();
        $request = (Object)[
            'uspsBulkLabel' => true,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'sender_address' => $this->senderAddress,
            'sender_city' => $this->senderCity,
            'sender_state' => $this->selectedState,
            'sender_zipcode' => $this->senderZipCode,
            'service' => $this->selectedService,
        ];

        $response = $usps_labelRepository->getRates($this->order, $request);
        if($response['success'] == true)
        {
            $this->uspsRate = $response['total_amount'];

        }else {

            $this->uspsRateError = $response['message'];
        }

    }
}
