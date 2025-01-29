<?php

namespace App\Services\IDLabel;

use Exception;
use App\Models\Order;
use App\Models\User;
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

    public function __construct()
    {
        $this->hasSuplimentary = false;
        $this->hasAnjunLabel = false;
        $this->corriosLogo = \public_path('images/correios-1.png');
        $this->partnerLogo =  public_path('images/chile-logo.png');
        $this->packetType = 'Packet Standard';
        $this->contractNumber = 'Contrato:  9912501576';
        $this->service = 2;
        $this->returnAddress = '<br> <br>';
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

    public function setPacketType(int $packetType)
    { 
        switch($packetType): 
            case Package::SERVICE_CLASS_HD_Express:
                $this->packetType = 'HD Express';
                $this->serviceLogo = public_path('images/standard-package.png');
            break; 
            default: 
            $this->packetType = 'HD Express';
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
        $this->items = $this->order->items->take($this->suplimentryAt());
        return $this;
    }

    private function setSuplimentryItems()
    {
        $suplimentryAt = $this->suplimentryAt();
        if ( $this->order->items->count() > $suplimentryAt){
            $this->hasSuplimentary = true;
            $this->sumplementryItems = $this->order->items->skip($suplimentryAt)->chunk(30);
        }
        return $this;
    }
    private function suplimentryAt(){ 
        foreach ( $this->order->items as  $key=>$item){
            if(strlen($item->description) >70  ){
                try{
                    
                if(strlen($this->order->items[$key+1]->description) <=70  && $key<2)
                    return $key==0?2:$key+1;
                }catch(Exception $e){
                    return $key==0?1:$key;

                } 
                 return $key==0?1:$key;
            }
        }
        return 4;
    }

    public function render()
    {
        return view('labels.id-label.cn23.index',$this->getViewData());
    }

    public function download()
    {
        if ( ! $this->order ){
            throw new Exception("Order not Set");
        }

        return \PDF::loadView('labels.id-label.cn23.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        if ( !file_exists(dirname($path)) ){
            mkdir(dirname($path),0775,true);
        }
        return \PDF::loadView('labels.id-label.cn23.index',$this->getViewData())->save($path);
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
}
