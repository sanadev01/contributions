<?php

namespace App\Http\Livewire;

use App\Models\Order;
use App\Models\ShippingService;
use Livewire\Component;

class ServiceSearch extends Component
{
    public $search;
    public $serviceId;
    public $selectedService = null;
    public $dropDownServices;
    public $allServices;
    public $serviceSubClass;
    public $order;
    public $rate = null;
    public $allRates = [];
    protected $listeners = ['removeService'];
    public function mount($allServices, $order)
    {
        $this->order = $order;
        $this->dropDownServices =  [];
        $this->allServices =  collect($allServices);
        if ($order->recipient->country_id != Order::US) {
            foreach ($this->allServices as $service) {
                $this->allRates[$service->id] = $service->getRateFor($order);
            }
        } else {
            foreach ($this->allServices as $service) {
                if ($service->is_inbound_domestic_service) {
                    $this->allRates[$service->id] = $service->getRateFor($order);
                }
            }
        }
        $this->serviceId = old('shipping_service_id', $order->shipping_service_id);
        if ($this->serviceId) {
            $this->selectedService = ShippingService::find($this->serviceId);
            $this->rate = $this->selectedService->getRateFor($order);
            if ($this->rate) {
                $this->search = optional($this->selectedService)->name . ' $' . $this->rate;
            } else {
                $this->search = $this->selectedService->name;
            }
            $this->selectService($this->serviceId);
        }
    }
    public function render()
    {
        return view('livewire.service-search');
    }
    public function updatedSearch()
    {
        $this->serviceId = null;
        if (!$this->search) {
            $this->dropDownServices = $this->allServices;
            $this->selectedService = null;
            $this->emit('clear-search');
            return;
        }
        $search = trim($this->search);
        $this->dropDownServices = $this->allServices->filter(function ($s) use ($search) {
            return stripos($s['name'], $search) !== false;
        });
    }
    public function handleFocus()
    {
        $this->dropDownServices = $this->allServices;
    }
    public function handleBlur()
    {
        $this->dropDownServices = [];
    }
    public function getShippingSubName($serviceId)
    {
        return ShippingService::find($serviceId)->sub_name;
    }
    public function selectService($serviceId)
    {
        $this->dropDownServices = [];
        $this->selectedService = ShippingService::find($serviceId);
        $this->rate = $this->getShippingRate($serviceId);
        $this->emit("service:updated", $this->serviceId, $this->selectedService->service_sub_class, $this->rate);
        $this->serviceId = $serviceId;
        if ($this->rate) {
            $this->search = $this->selectedService->name . ' $' . $this->rate;
        } else {
            $this->search = $this->selectedService->name;
        }
    }
    public function removeService()
    {

        $this->selectedService = null;
        $this->search = '';
    }


    public function getShippingRate($serviceId)
    {
        if (isset($this->allRates[$serviceId])) {
            $rate = $this->allRates[$serviceId];
            return $rate > 0 ? $rate : '';
        }
        return  'null';
    }
}
