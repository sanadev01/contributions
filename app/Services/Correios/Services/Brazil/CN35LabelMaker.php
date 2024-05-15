<?php

namespace App\Services\Correios\Services\Brazil;

use Carbon\Carbon;
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
    private $containerGroup;

    public function __construct(Container $container)
    {
        $this->companyName = '<img src="' . public_path('images/hd-1cm.png') . '" style="height:1cm;display:block;position:absolute:top:0;left:0;"/>';
        $this->packetType = 'PACKET STANDARD';
        $this->officeAddress = '';
        $this->serialNumber = 1;
        $this->flightNumber = '';
        $this->dispatchDate = '';
        $this->containerGroup = '';

        $order = $container->orders->first();

        if ($order) {
            $this->setType($order->getOriginalWeight('kg'));
        }
        $this->weight =  $container->total_weight;
        $this->dispatchNumber = $container->dispatch_number;
        $this->originAirpot = 'MIA';
        $this->setService($container->service_code);
        $this->destinationAirport = $container->destination_ariport;
        $this->itemsCount = $container->total_orders;
        $this->unitCode = $container->unit_code;
        $firstOrderDate = optional($container->orders->first())->order_date;
        if (optional($firstOrderDate)->greaterThanOrEqualTo(Carbon::parse('2024-01-22'))) {
            $this->containerGroup = $container->getGroup($container);
        }
    }

    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function setService(int $service)
    {
        $this->service = $service;

        if (in_array($this->service, [1, 9, 18, 21])) {
            $this->packetType = 'PACKET EXPRESS';
        }
        if (in_array($this->service, [2, 8, 19, 20])) {
            $this->packetType = 'PACKET STANDARD';
        }
        if ($this->service == 3) {
            $this->packetType = 'PACKET MINI';
        }
        if ( $this->service == 23 ){
            $this->packetType = 'PasarEx';
        }
        return $this;
    }
    public function setPacketType($packetType)
    {
        $this->packetType = $packetType;
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
        if ($weight > 3) {
            if ($this->packetType == 'PACKET EXPRESS') {
                $this->officeAddress = 'Empresa Brasileira de Correios e Telégrafos <br/>
                                        Centro Internacional de São Paulo – SE/SPM <br/>
                                        Rua Mergenthaler, 592 – Bloco III, 5 Mezanino <br/>
                                        05311-900  Vila Leopoldina - São Paulo/SP <br/>
                                        CNPJ 34.028.316/7105-85';
                return $this;
            }
            if ($this->packetType == 'PACKET STANDARD') {
                $this->officeAddress = 'Empresa Brasileira de Correios e Telégrafos <br/> 
                                        Centro Internacional do Rio de Janeiro –SE/RJ <br/>
                                        Ponta do Galeão, s/n 2 andar TECA Correios Galeão, <br/>
                                        21941-9740 Ilha do Governador, Rio de Janeiro/RJ <br/>
                                        CNPJ: 34.028.316/7189-93';
                return $this;
            }
        }
        $this->officeAddress = 'Empresa Brasileira de Correios e Telégrafos <br/>
                                Centro Internacional de Curitiba –SE/PR <br/>
                                Rua Salgado Filho, 476 Jardim Amélia <br/>
                                83.330-972  Pinhais/PR <br/>
                                CNPJ 34.028.316/0031-29';

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
        return view('labels.brazil.cn35.index', $this->getViewData());
    }

    public function download()
    {
        return \PDF::loadView('labels.brazil.cn35.index', $this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        return \PDF::loadView('labels.brazil.cn35.index', $this->getViewData())->save($path);
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
            'containerGroup' => $this->containerGroup
        ];
    }
}
