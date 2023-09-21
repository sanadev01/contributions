<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\BillingInformation;

class AuthorizeCard extends Component
{   

    public $billingInformationId;
    public $withRequiredInput;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($billingInformationId,$withRequiredInput=true)
    {
        $this->withRequiredInput = $withRequiredInput;
        $this->billingInformationId = $billingInformationId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.authorizecard', [
            'billingInformation' => BillingInformation::find($this->billingInformationId)
        ]);
    }
}
