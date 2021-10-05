<?php

namespace App\Http\Livewire\Label;

use App\Models\Order;
use App\Models\State;
use Livewire\Component;
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

    public function render()
    {
        $this->getStates();
        if($this->shippingServices != null)
        {
            $this->dispatchBrowserEvent('sender-modal', ['shippingServices' => $this->shippingServices]);
        }
        return view('livewire.label.buy-usps-label', compact($this->shippingServices));
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
        $this->getShippingServices();
        // dd($this->shippingServices);
        $this->dispatchBrowserEvent('sender-modal');
    }

    public function getStates()
    {
        $this->states = State::query()->where("country_id", 250)->get(["name","code","id"]);
    }

    public function getShippingServices()
    {
        $usps_labelRepository = new USPSLabelRepository();
        $this->shippingServices = $usps_labelRepository->getShippingServices($this->order);
    }

    protected $rules = [
        'selectedState' => 'required',
        'senderAddress' => 'required',
        'senderCity' => 'required',
    ];

    public function updatedselectedState()
    {
        $this->validate();
        $usps_labelRepository = new USPSBulkLabelRepository();
        $this->order = $usps_labelRepository->handle($this->selectedOrders);
        $this->getShippingServices();
    }
}
