<?php

namespace App\Services\Correios\Services\Brazil;

use Exception;
use App\Models\Order;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Services\Correios\Contracts\HasLableExport;

class CN23LabelMaker implements HasLableExport
{

    private $order;
    private $recipient;
    private $corriosLogo;
    private $partnerLogo;
    private $packetType;
    private $contractNumber;
    private $service;
    private $returnAddress;
    private $complainAddress;
    private $items;
    private $sumplementryItems;
    private $hasSuplimentary;

    public function __construct()
    {
        $this->hasSuplimentary = false;
        $this->corriosLogo = \public_path('images/correios-1.png');
        $this->partnerLogo =  public_path('images/hd-label-logo-1.png');
        $this->packetType = 'Packet Standard';
        $this->contractNumber = 'Contrato:  9912501576';
        $this->service = 2;
        $this->returnAddress = 'Rua Barão do Triunfo 427 <br> 15and CJ 151 Brooklyn <br> 04602/001 São Paulo, Brazil';
        $this->complainAddress = 'Em caso de problemas com o produto, entre em contato com o remetente';
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
        $this->recipient = $order->recipient;
        $this->order->load('items');
        $this->setItems()->setSuplimentryItems();
        return $this;
    }

    public function setCorrieosLogo($logoPath)
    {
        $this->corriosLogo = $logoPath;
        return $this;
    }

    public function setPacketType($packetType)
    {
        $this->packetType = $packetType;
    }

    public function setPartnerLogo($logoPath)
    {
        $this->partnerLogo = $logoPath;
        return $this;
    }

    public function setService(int $service)
    {
        $this->service = $service;
        return $this;
    }

    public function setReturnAddress($address)
    {
        $this->returnAddress = $address;
        return $this;
    }

    public function compainAddress($address)
    {
        $this->complainAddress = $address;
        return $this;
    }

    private function setItems()
    {
        $this->items = $this->order->items->take(4);
        return $this;
    }

    private function setSuplimentryItems()
    {
        if ( $this->order->items->count() > 4 ){
            $this->hasSuplimentary = true;
            $this->sumplementryItems = $this->order->items->chunk(30);
        }

        return $this;
    }

    public function render()
    {
        return view('labels.brazil.cn23.index',$this->getViewData());
    }

    public function download()
    {
        if ( ! $this->order ){
            throw new Exception("Order not Set");
        }

        return \PDF::loadView('labels.brazil.cn23.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        if ( !file_exists(dirname($path)) ){
            mkdir(dirname($path),0775,true);
        }
        return \PDF::loadView('labels.brazil.cn23.index',$this->getViewData())->save($path);
    }

    private function getViewData()
    {
        return [
            'order' => $this->order,
            'recipient' => $this->recipient,
            'corriosLogo' => $this->corriosLogo,
            'partnerLogo' => $this->partnerLogo,
            'packetType' => $this->packetType,
            'contractNumber' => $this->contractNumber,
            'returnAddress' => $this->returnAddress,
            'complainAddress' => $this->complainAddress,
            'service' => $this->service,
            'items' => $this->items,
            'suplimentaryItems' => $this->sumplementryItems,
            'hasSumplimentary' => $this->hasSuplimentary,
            'barcodeNew' => new BarcodeGeneratorPNG(),
        ];
    }
}
