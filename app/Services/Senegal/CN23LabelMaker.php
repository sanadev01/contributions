<?php

namespace App\Services\Senegal;

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
    private $service;
    private $serviceLogo;
    private $items;
    private $sumplementryItems;
    private $hasSuplimentary;

    public function __construct()
    {
        $this->hasSuplimentary = false;
        $this->corriosLogo = \public_path('images/correios-1.png');
        $this->partnerLogo =  public_path('images/hd-label-logo-1.png');
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

    public function setService(int $service)
    {
        $this->service = $service;
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
        return view('labels.senegal.cn23.index',$this->getViewData());
    }

    public function download()
    {
        if ( ! $this->order ){
            throw new Exception("Order not Set");
        }

        return \PDF::loadView('labels.senegal.cn23.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        if ( !file_exists(dirname($path)) ){
            mkdir(dirname($path),0775,true);
        }
        return \PDF::loadView('labels.senegal.cn23.index',$this->getViewData())->save($path);
    }

    private function getViewData()
    {
        return [
            'order' => $this->order,
            'recipient' => $this->recipient,
            'corriosLogo' => $this->corriosLogo,
            'partnerLogo' => $this->partnerLogo,
            'serviceLogo' => $this->serviceLogo,
            'service' => $this->service,
            'items' => $this->items,
            'suplimentaryItems' => $this->sumplementryItems,
            'hasSumplimentary' => $this->hasSuplimentary,
            'barcodeNew' => new BarcodeGeneratorPNG(),
        ];
    }
}
