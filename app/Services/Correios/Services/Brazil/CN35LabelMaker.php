<?php 

namespace App\Services\Correios\Services\Brazil;

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

    public function __construct()
    {
        $this->companyName = '<img src="'.public_path('images/hd-1cm.png').'" style="height:1cm;display:block;position:absolute:top:0;left:0;"/>';
        $this->packetType = 'PACKET STANDARD';
        $this->officeAddress = '
                                Empresa Brasileira de Correios e Telégrafos <br/> 
                                Centro Internacional de Curitiba - DR / PR <br/>
                                Rua Salgado Filho, 476, Jardim Amélia - Pinhais / PR <br/>
                                CEP: 80.330-972 <br/>
                                CNPJ: 34.028.316/0031-29';
        $this->serialNumber = 1;
        $this->flightNumber = '';
        $this->dispatchDate = '';
    }

    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function setService(int $service)
    {
        $this->service = $service;

        if ( $this->service == 1 ){
            $this->packetType = 'PACKET EXPRESS';
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
        return view('labels.brazil.cn35.index',$this->getViewData());
    }

    public function download()
    {
        return \PDF::loadView('labels.brazil.cn35.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        return \PDF::loadView('labels.brazil.cn35.index',$this->getViewData())->save($path);
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
        ];
    }

}