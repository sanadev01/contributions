<?php

namespace App\Repositories;

class USLabelRepository
{
    protected $upsLabelRepository;
    protected $uspsLabelRepository;
    public $upsShippingServices;
    public $uspsShippingServices;
    public $upsErrors;
    public $uspsErrors;

    public function __construct(UPSLabelRepository $upsLabelRepository, USPSLabelRepository $uspsLabelRepository)
    {
        $this->upsLabelRepository = $upsLabelRepository;
        $this->uspsLabelRepository = $uspsLabelRepository;
    }

    public function shippingServices($order)
    {
        $shippingServices = collect();

        $this->upsShippingServices =  $this->upsLabelRepository->getShippingServices($order);
        $this->upsErrors = $this->upsLabelRepository->getUPSErrors();

        $this->uspsShippingServices =  $this->uspsLabelRepository->getShippingServices($order);
        $this->uspsErrors = $this->uspsLabelRepository->getUSPSErrors();

        if ($this->upsShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->upsShippingServices);
        }

        if ($this->uspsShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->uspsShippingServices);
        }

        return $shippingServices;
    }

    public function getErrors()
    {
        $errors = [];

        if ($this->upsErrors) 
        {
            array_push($errors, $this->upsErrors);
        }

        if ($this->uspsErrors) 
        {
            array_push($errors, $this->uspsErrors);
        }

        return $errors;
    }
}
