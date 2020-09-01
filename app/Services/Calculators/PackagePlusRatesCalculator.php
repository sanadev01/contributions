<?php

namespace App\Services\Calculators;

use Exception;
use App\Models\Order;
use App\Models\Setting;
use App\Models\PackagePlusRate;

class PackagePlusRatesCalculator extends AbstractRateCalculator
{
    private $packagePlusRates;

    private $toState;

    public function __construct(Order $order)
    {
        $this->toState = $order->address->uf ? $order->address->uf : 'SP';

        $this->packagePlusRates = PackagePlusRate::where('uf', strtoupper($this->toState))->first();

        $this->setServiceId(self::SERVICE_PACKAGE_PLUS);

        $this->setName('Package Plus');

        parent::__construct($order);
    }

    public function isAvailable()
    {
        try {
            if (! setting(Setting::SHIPPING_SERVICE_PREFIX.$this->getServiceId(), null, null, true)) {
                return false;
            }

            if (! setting(Setting::PACKAGE_PLUS_FREIGHT, null, null, true)) {
                return false;
            }

            if (! $this->packagePlusRates || ! $this->packagePlusRates->capital || ! $this->packagePlusRates->interior) {
                return false;
            }

            if ($this->order->address->country->code == 'BR') {
                return true;
            }
        } catch (Exception $exception) {
            return false;
        }
    }

    public function getRate($addProfit = true)
    {
        $totalCost = ($this->getFreightCost() * $this->weight) + $this->getDestinationCost();

        if (! $addProfit) {
            return $totalCost;
        }

        return $totalCost + $this->getProfitOn($totalCost);
    }

    private function getFreightCost()
    {
        return setting(Setting::PACKAGE_PLUS_FREIGHT, 0, null, true);
    }

    private function getDestinationCost()
    {
        $additionalPerKgs = 0;
        $weight = ceil($this->weight);

        switch (strtolower($this->address->stateType)) {
            case 'capital':
                $destinationCharges = $this->packagePlusRates->capital;
                $additionalPerKgs = $this->packagePlusRates->capital_extra['additional_kg'];
                break;

            default:
                $destinationCharges = $this->packagePlusRates->interior;
                $additionalPerKgs = $this->packagePlusRates->interior_extra['additional_kg'];
                break;
        }

        /**
         * Additional Charges if weight exceeds 100kg.
         */
        $additionalKgCharges = 0;
        $additionalKgs = 0;
        if ($weight > 100) {
            $additionalKgs = ($weight - 100);
            $additionalKgCharges = $additionalKgs * $additionalPerKgs;
        }

        $normalKgCost = $destinationCharges[$weight - $additionalKgs];

        return $normalKgCost + $additionalKgCharges;
    }

    public function weightUnit()
    {
        return 'KG';
    }

    public function weightInDefaultUnit()
    {
        return $this->weight;
    }
}
