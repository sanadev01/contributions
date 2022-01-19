<?php

namespace App\Repositories;

use App\Facades\USPSFacade;

class DomesticLabelRepository
{
    public function validateAddress($request)
    {
        return $this->callForUSPSAddressApi($request);
    }

    public function getRatesForDomesticServices($request, $usShippingServices)
    {
        dd($request->all(), $usShippingServices);
    }

    private function callForUSPSAddressApi($request)
    {
        return USPSFacade::validateAddress($request);
    }
}
