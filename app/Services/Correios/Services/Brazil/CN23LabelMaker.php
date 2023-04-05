<?php

namespace App\Services\Correios\Services\Brazil;

use Exception;
use App\Models\Order;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Services\Correios\Contracts\HasLableExport;
use App\Services\Correios\Models\Package;

class CN23LabelMaker implements HasLableExport
{

    private $order;
    private $recipient;
    private $corriosLogo;
    private $partnerLogo;
    private $packetType;
    private $contractNumber;
    private $hasAnjunLabel;
    private $service;
    private $serviceLogo;
    private $returnAddress;
    private $complainAddress;
    private $items;
    private $sumplementryItems;
    private $hasSuplimentary;
    private $hasDescpCount;

    public function __construct()
    {
        $this->hasSuplimentary = false;
        $this->hasAnjunLabel = false;
        $this->corriosLogo = \public_path('images/correios-1.png');
        $this->partnerLogo =  public_path('images/hd-label-logo-1.png');
        $this->packetType = 'Packet Standard';
        $this->contractNumber = 'Contrato:  9912501576';
        $this->service = 2;
        $this->returnAddress = 'Blue Line Ag. De Cargas Ltda. <br>
        Rua Barao Do Triunfo, 520 - CJ 152 - Brooklin Paulista
        CEP 04602-001 - São Paulo - SP- Brasil';
        $this->complainAddress = 'Em caso de problemas com o produto, entre em contato com o remetente';
        $this->hasDescpCount = '';
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
        $this->recipient = $order->recipient;
        $this->order->load('items');
        $this->setItems()->setSuplimentryItems();

        if ($this->order->shippingService->isAnjunService()) {
            $this->contractNumber = 'Contrato:  9912501700';
            $this->hasAnjunLabel = true;
        }
        return $this;
    }

    public function setCorrieosLogo($logoPath)
    {
        $this->corriosLogo = $logoPath;
        return $this;
    }

    public function setPacketType(int $packetType)
    {
        switch($packetType):
            case Package::SERVICE_CLASS_EXPRESS:
                $this->packetType = 'Packet Express';
                $this->serviceLogo = public_path('images/express-package.png');
            break;
            case Package::SERVICE_CLASS_MINI:
                $this->packetType = 'Packet Mini';
                $this->serviceLogo = public_path('images/mini-package.png');
            break;
            case Package::SERVICE_CLASS_STANDARD:
            default:
                $this->packetType = 'Packet Standard';
                $this->serviceLogo = public_path('images/standard-package.png');
            break;
        endswitch;

        return $this;
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
        $this->getItemsDescpCount($this->order);
        
        if($this->hasDescpCount[0]['count'] > 190) {
            $this->items = $this->order->items->take(1);
        }elseif($this->hasDescpCount[1]['count'] > 100){
            $this->items = $this->order->items->take(2);
        }elseif($this->hasDescpCount[2]['count'] > 190){
            $this->items = $this->order->items->take(3);
        }else {
            $this->items = $this->order->items->take(4);
        }
        return $this;
    }

    private function setSuplimentryItems()
    {
        if ( $this->order->items->count() > 1 ){
            $this->hasSuplimentary = true;
            $this->getItemsDescpCount($this->order);
            if($this->hasDescpCount > 190) {
                $this->sumplementryItems = $this->order->items->skip(1)->chunk(30);
            }else {
                $this->sumplementryItems = $this->order->items->skip(4)->chunk(30);
            }
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
            'serviceLogo' => $this->serviceLogo,
            'packetType' => $this->packetType,
            'contractNumber' => $this->contractNumber,
            'hasAnjunLabel' => $this->hasAnjunLabel,
            'returnAddress' => $this->returnAddress,
            'complainAddress' => $this->complainAddress,
            'service' => $this->service,
            'items' => $this->items,
            'suplimentaryItems' => $this->sumplementryItems,
            'hasSumplimentary' => $this->hasSuplimentary,
            'barcodeNew' => new BarcodeGeneratorPNG(),
        ];
    }

    private function getItemsDescpCount (Order $order) {
        $countDescp = [];
        foreach ($order->items as $key => $item) {
            $count= strlen($item->description);
            $countToPush = ['count' => $count];
           array_push($countDescp, $countToPush);
        }
        // dd($countDescp);
        // $descp = array_column($countDescp, 'count');
        // $this->hasDescpCount = array_sum($descp);
        return $this->hasDescpCount = $countDescp;
    }
}
