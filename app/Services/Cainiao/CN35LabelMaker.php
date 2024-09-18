<?php

namespace App\Services\Cainiao;

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
    private $colombiaContainer = false;

    public function __construct(Container $container)
    {
        $this->companyName = 'Cainiao';
        $this->packetType = 'PACKET STANDARD';
        $this->officeAddress = '';
        $this->serialNumber = 1;
        $this->flightNumber = $container->flight_number;
        $this->dispatchDate = Carbon::now()->format('Y-m-d');
        $order = $container->orders->first();
        if ($order) {
            $this->OrderWeight = $order->getOriginalWeight('kg');
        }

        $this->weight =  $container->getWeight();
        $this->dispatchNumber = $container->dispatch_number;
        $this->service = $container->getServiceCode();
        $this->destinationAirport = $container->destination_operator_name;
        $this->itemsCount = $container->getPiecesCount();
        $this->unitCode = $container->getUnitCode();
        $this->originAirpot = 'HKG';
        $this->destinationAirport = "GRU";
        $this->officeAddress = "Empresa Brasileira de Correios e Telégrafos<br>
                                Centro Internacional de Curitiba - SE/PR<br>
                                Rua Salgado Filho, 476, Jardim Amélia<br>
                                83330-972 - Pinhais/PR<br>
                                CNPJ: 34.028.316/9148-22";
    }

    public function render()
    {
        return view('labels.cainiao.cn35.index', $this->getViewData());
    }

    public function download()
    {
        return \PDF::loadView('labels.cainiao.cn35.index', $this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        return \PDF::loadView('labels.cainiao.cn35.index', $this->getViewData())->save($path);
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
