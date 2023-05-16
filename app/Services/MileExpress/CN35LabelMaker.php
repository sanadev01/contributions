<?php 

namespace App\Services\MileExpress;

use App\Models\Warehouse\Container;
use App\Services\Correios\Contracts\HasLableExport;

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
    private $colombiaContainer = false;

    public function __construct(Container $container)
    {
        $this->companyName = '<img src="'.public_path('images/hd-1cm.png').'" style="height:1cm;display:block;position:absolute:top:0;left:0;"/>';
        $this->packetType = 'PACKET STANDARD';
        $this->officeAddress = '';
        $this->serialNumber = 1;
        $this->flightNumber = '';
        $this->dispatchDate = '';
        $order = $container->orders->first();        
        if($order){ 
              $this->setType($order->getOriginalWeight('kg')); 
        }
        
        $this->weight =  $container->getWeight();
        $this->dispatchNumber = $container->dispatch_number;
        $this->originAirpot = 'MIA';
        $this->setService($container->getServiceCode());
        $this->destinationAirport = $container->getDestinationAriport();        
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

        if ( $this->service == 15 ){
            $this->packetType = 'Homedelivebr Express';
            $this->companyName = 'HD Express';
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
        $this->officeAddress = 'Express courier'; 
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
        return view('labels.milli-express.cn35.index',$this->getViewData());
    }

    public function download()
    {
        return \PDF::loadView('labels.milli-express.cn35.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        return \PDF::loadView('labels.milli-express.cn35.index',$this->getViewData())->save($path);
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
            'colombiaContainer' => $this->colombiaContainer,
        ];
    }

}