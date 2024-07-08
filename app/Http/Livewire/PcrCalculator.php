<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PcrCalculator extends Component
{
    public $nonPcrCostOfProduct = 0.0;
    public $nonPcrShippingCost = 0.0;
    public $nonPcrInsurance = 0.0;
    public $pcrCostOfProduct = 0.0;
    public $pcrShippingCost = 0.0;
    public $pcrInsurance = 0.0;
    public $pcrTotalTaxAndDuty = 0.0;
    public $nonPcrTotalTaxAndDuty  = 0.0;

    public function updated($propertyName)
    {
        if (str_contains($propertyName, 'pcr')) {
            $this->calculatePcr();
        } else {
            $this->calculateNonPcr();
        }
    }

    public function calculatePcr()
    {
        $pcrCostOfProduct = floatval($this->pcrCostOfProduct ?: 0);
        $pcrShippingCost = floatval($this->pcrShippingCost ?: 0);
        $pcrInsurance = floatval($this->pcrInsurance ?: 0);

        $totalCost = $pcrInsurance + $pcrShippingCost + $pcrCostOfProduct;
        $duty = $totalCost > 50 ? (($totalCost * .60) - 20) : $totalCost * 0.2; // Duties
        $totalCostOfTheProduct = $totalCost + $duty; // Total Cost Of Product
        $icms = 0.17;  // ICMS (IVA)
        $totalIcms = $totalCostOfTheProduct * $icms; // Total ICMS (IVA)
        $this->pcrTotalTaxAndDuty = round($duty + $totalIcms, 2); // Total Taxes & Duties
    }

    public function calculateNonPcr()
    {
        $nonPcrCostOfProduct = floatval($this->nonPcrCostOfProduct ?: 0);
        $nonPcrShippingCost = floatval($this->nonPcrShippingCost ?: 0);
        $nonPcrInsurance = floatval($this->nonPcrInsurance ?: 0);

        $totalCost = $nonPcrInsurance + $nonPcrShippingCost + $nonPcrCostOfProduct;
        $duty = $totalCost * .60; // Duties
        $totalCostOfTheProduct = $totalCost + $duty; // Total Cost Of Product
        $icms = 0.17;  // ICMS (IVA)
        $totalIcms = $totalCostOfTheProduct * $icms; // Total ICMS (IVA)
        $this->nonPcrTotalTaxAndDuty = round($duty + $totalIcms, 2); // Total Taxes & Duties
    }

    public function render()
    {
        return view('livewire.pcr-calculator');
    }
}
