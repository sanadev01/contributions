<?php 

namespace App\Services\GDE;

use App\Models\Warehouse\Container;
use App\Services\Correios\Contracts\HasLableExport;
use Carbon\Carbon;

class CN35LabelMaker implements HasLableExport
{

    private $companyName;
    private $packetType;
    private $dispatchNumber;
    private $officeAddress;
    private $serialNumber;
    private $flightNumber;
    private $dispatchDate;
    private $originAirpot;
    private $destinationAirport;
    private $itemsCount;
    private $weight;
    private $service;
    private $unitCode;
    private $OrderWeight;

    public function __construct(Container $container)
    {
 

        $this->companyName = 'HomeDelivery Express Services';
        $this->packetType = 'PACKET STANDARD';
        $this->officeAddress = '';
        $this->serialNumber = 1;
        $this->flightNumber = $container->flight_number;
        $this->dispatchDate = Carbon::now()->format('Y-m-d');
        
        $order = $container->orders->first();
        
        if($order){ 
              $this->setType($order->getOriginalWeight('kg')); 
        }
        
        $this->weight =  $container->getWeight();
        $this->dispatchNumber = $container->dispatch_number;
        $this->originAirpot =  $container->origin_airport;
        $this->setService($container->getServiceCode());
        $this->destinationAirport = $container->destination_operator_name;        
        $this->itemsCount = $container->getPiecesCount();
        $this->unitCode = $container->getUnitCode();
    }

    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function setService(int $service)
    {
        $this->service = $service;

        if ( $this->service == 1 || $this->service == 9 ) {
            $this->packetType = 'PACKET EXPRESS';
        }
        if ( $this->service == 2 || $this->service == 8 ) {
            $this->packetType = 'PACKET STANDARD';
        }
        if ( $this->service == 3 ){
            $this->packetType = 'PACKET MINI';
        }

        return $this;
    }

    public function setDispatchNumber(string $dispatchNumber)
    {
        $this->dispatchNumber = $dispatchNumber;
        return $this;
    }

    public function setOfficeAddress(string $address)
    {
        $this->officeAddress = $address;
        return $this;
    }

    public function setSerialNumber(int $serialNumber)
    {
        $this->serialNumber = $serialNumber;
        return $this;
    }

    public function setFlightNumber(string $flightNumber)
    {
        $this->flightNumber = $flightNumber;
        return $this;
    }

    public function setDispatchDate(string $date)
    {
        $this->dispatchDate = $date;
        return $this;
    }

    public function setOriginAirport(string $airport)
    {
        $this->originAirpot = $airport;
        return $this;
    }

    public function setType(string $weight)
    {
        $this->OrderWeight = $weight;
        $this->officeAddress = "Herco Freight Forwarders Inc <br> 2200 NW 190<sup>th</sup> Avenue Suite#100  <br> Miami , FL 33182 Phone (305) 888-5191";

        return $this;
    }
    public function setDestinationAirport(string $airport)
    {
        $this->destinationAirport = $airport;
        return $this;
    }

    public function setItemsCount(string $itemsCount)
    {
        $this->itemsCount = $itemsCount;
        return $this;
    }

    public function setWeight(string $weight)
    {
        $this->weight = $weight;
        return $this;
    }

    public function setUnitCode(string $unitCode)
    {
        $this->unitCode = $unitCode;
        return $this;
    }

    public function render()
    {
        return view('labels.gde.cn35.index',$this->getViewData());
    }

    public function download()
    {
        return \PDF::loadView('labels.gde.cn35.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        return \PDF::loadView('labels.gde.cn35.index',$this->getViewData())->save($path);
    }

    private function getViewData()
    {
        return [
            'companyName' => $this->companyName,
            'packetType' => $this->packetType,
            'dispatchNumber' => $this->dispatchNumber,
            'officeAddress' => $this->officeAddress,
            'serialNumber' => $this->serialNumber,
            'flightNumber' => $this->flightNumber,
            'dispatchDate' => $this->dispatchDate,
            'originAirpot' => $this->originAirpot,
            'destinationAirport' => $this->destinationAirport,
            'itemsCount' => $this->itemsCount,
            'weight' => $this->weight,
            'service' => $this->service,
            'unitCode' => $this->unitCode,
            'OrderWeight' => $this->OrderWeight,
        ];
    }

}