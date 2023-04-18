<?php

namespace App\Services\GDE;

use Exception;
use App\Models\Order;
use Barryvdh\DomPDF\PDF;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Services\Correios\Models\Package;
use App\Services\Correios\Contracts\HasLableExport;

class GDELabelMaker implements HasLableExport
{
    private $order;
    private $recipient;
    private $partnerLogo;
    private $complainAddress;
    private $items;
    private $sumplementryItems;
    private $hasSuplimentary;

    public function __construct()
    {
        $this->hasSuplimentary = false;
        $this->partnerLogo =  public_path('images/hd-label-logo-1.png');
        $this->packetType = 'Packet SRP';
        $this->contractNumber = 'Contrato:  9912501576';
        $this->service = 2;
        $this->returnAddress = 'Blue Line Ag. De Cargas Ltda. <br>
        Rua Barao Do Triunfo, 520 - CJ 152 - Brooklin Paulista
        CEP 04602-001 - São Paulo - SP- Brasil';
        $this->complainAddress = 'In case of any question please contact the shipper';
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

    public function setPartnerLogo($logoPath)
    {
        $this->partnerLogo = $logoPath;
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
        return view('labels.gde.index',$this->getViewData());
    }

    public function download()
    {
        if ( ! $this->order ){
            throw new Exception("Order not Set");
        }

        return \PDF::loadView('labels.gde.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        if ( !file_exists(dirname($path)) ){
            mkdir(dirname($path),0775,true);
        }
        return \PDF::loadView('labels.gde.index',$this->getViewData())->save($path);
    }

    private function getViewData()
    {
        return [
            'order' => $this->order,
            'recipient' => $this->recipient,
            'partnerLogo' => $this->partnerLogo,
            'complainAddress' => $this->complainAddress,
            'items' => $this->items,
            'suplimentaryItems' => $this->sumplementryItems,
            'hasSumplimentary' => $this->hasSuplimentary,
            'barcodeNew' => new BarcodeGeneratorPNG(),
        ];
    }
}
