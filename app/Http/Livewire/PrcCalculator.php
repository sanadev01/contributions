<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PrcCalculator extends Component
{
    public $nonPrcCostOfProduct = 0.0;
    public $nonPrcShippingCost = 0.0;
    public $nonPrcInsurance = 0.0;
    public $prcCostOfProduct = 0.0;
    public $prcShippingCost = 0.0;
    public $prcInsurance = 0.0;
    public $prcTotalTaxAndDuty = 0.0;
    public $nonPrcTotalTaxAndDuty  = 0.0;

    public function updated($propertyName)
    {
        if (str_contains($propertyName, 'prc')) {
            $this->calculatePrc();
        } else {
            $this->calculateNonPrc();
        }
    }

    public function calculatePrc()
    {
        $prcCostOfProduct = floatval($this->prcCostOfProduct ?: 0);
        $prcShippingCost = floatval($this->prcShippingCost ?: 0);
        $prcInsurance = floatval($this->prcInsurance ?: 0);

        $totalCost = $prcInsurance + $prcShippingCost + $prcCostOfProduct;
        $duty = $totalCost > 50 ? (($totalCost * .60) - 20) : $totalCost * 0.2; // Duties
        $totalCostOfTheProduct = $totalCost + $duty; // Total Cost Of Product
        $icms = 0.17;  // ICMS (IVA)
        $totalIcms = $totalCostOfTheProduct * $icms; // Total ICMS (IVA)
        $this->prcTotalTaxAndDuty = round($duty + $totalIcms, 2); // Total Taxes & Duties
    }

    public function calculateNonPrc()
    {
        $nonPrcCostOfProduct = floatval($this->nonPrcCostOfProduct ?: 0);
        $nonPrcShippingCost = floatval($this->nonPrcShippingCost ?: 0);
        $nonPrcInsurance = floatval($this->nonPrcInsurance ?: 0);

        $totalCost = $nonPrcInsurance + $nonPrcShippingCost + $nonPrcCostOfProduct;
        $duty = $totalCost * .60; // Duties
        $totalCostOfTheProduct = $totalCost + $duty; // Total Cost Of Product
        $icms = 0.17;  // ICMS (IVA)
        $totalIcms = $totalCostOfTheProduct * $icms; // Total ICMS (IVA)
        $this->nonPrcTotalTaxAndDuty = round($duty + $totalIcms, 2); // Total Taxes & Duties
    }

    public function render()
    {
        return view('livewire.prc-calculator');
    }
}
