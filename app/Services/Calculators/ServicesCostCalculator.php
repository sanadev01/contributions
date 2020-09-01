<?php

namespace App\Services\Calculators;

use App\Models\Service;
use Illuminate\Support\Collection;

class ServicesCostCalculator
{
    private $shipmentCost;
    private $services;

    public function __construct(Collection $services, $shipmentCost = 0)
    {
        $this->shipmentCost = $shipmentCost;
        $this->services = $services;
    }

    public function handle()
    {
        foreach ($this->services as $service) {
            $this->calculateForService($service);
        }

        return $this->services;
    }

    private function calculateForService(Service $service)
    {
        if ($service->isQtyRequired()) {
            $service->total = $service->price * $service->pivot->qty;
            $service->totalCost = $service->cost * $service->pivot->qty;
            $service->profit = ($service->total - $service->totalCost);
        } elseif ($service->isRateInPercent()) {
            $service->total = ($service->price * $this->shipmentCost / 100);
            $service->totalCost = $service->cost;
            $service->profit = $service->total - $service->totalCost;
            dd($service);
        } else {
            $service->total = $service->price;
            $service->totalCost = $service->cost;
            $service->profit = $service->price - $service->cost;
        }

        return $service;
    }
}
