<?php

namespace App\Repositories;

class USLabelRepository
{
    protected $upsLabelRepository;
    protected $uspsLabelRepository;
    protected $fedExLabelRepository;
    public $upsShippingServices;
    public $uspsShippingServices;
    public $fedExShippingServices;
    public $upsErrors;
    public $uspsErrors;
    public $fedExErrors;

    public function __construct(UPSLabelRepository $upsLabelRepository, USPSLabelRepository $uspsLabelRepository, FedExLabelRepository $fedExLabelRepository)
    {
        $this->upsLabelRepository = $upsLabelRepository;
        $this->uspsLabelRepository = $uspsLabelRepository;
        $this->fedExLabelRepository = $fedExLabelRepository;
    }

    public function shippingServices($order)
    {
        $shippingServices = collect();

        $this->upsShippingServices =  $this->upsLabelRepository->getShippingServices($order);
        $this->upsErrors = $this->upsLabelRepository->getUPSErrors();

        $this->uspsShippingServices =  $this->uspsLabelRepository->getShippingServices($order);
        $this->uspsErrors = $this->uspsLabelRepository->getUSPSErrors();

        $this->fedExShippingServices =  $this->fedExLabelRepository->getShippingServices($order);
        $this->fedExErrors = $this->fedExLabelRepository->getFedExErrors();

        if ($this->upsShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->upsShippingServices);
        }

        if ($this->uspsShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->uspsShippingServices);
        }

        if ($this->fedExShippingServices->isNotEmpty()) 
        {
            $shippingServices = $shippingServices->merge($this->fedExShippingServices);
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

        if ($this->fedExErrors) 
        {
            array_push($errors, $this->fedExErrors);
        }

        return $errors;
    }
}
