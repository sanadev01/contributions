<?php 

namespace App\Services\CorreosChile;

use App\Services\Correios\Contracts\HasLableExport;

class CorreosChileCN35LabelMaker implements HasLableExport
{
    private $companyName;
    private $dispatchNumber;
    private $serialNumber;
    private $dispatchDate;
    private $originAirpot;
    private $destinationAirport;
    private $itemsCount;
    private $weight;
    private $service;
    private $seal_no;
    private $awb;

    public function __construct()
    {
        $this->companyName = '<img src="'.public_path('images/hd-1cm.png').'" style="height:1cm;display:block;position:absolute:top:0;left:0;"/>';
       
        $this->serialNumber = 1;
       
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

        if ( $this->service == 4 ){
            $this->service = 'SRM';
        }
        if ( $this->service == 5 ){
            $this->service = 'SRP';
        }

        return $this;
    }

    public function setsealNumber(string $sealNumber)
    { 
        $this->seal_no = $sealNumber;
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

    public function setAwbNumber(string $awb = null)
    {
        $this->awb = $awb;
        return $this;
    }

    public function get_code_for_generating_barcode()
    { 
        return $this->seal_no;
    }


    public function render()
    {
        return view('labels.chile.cn35.index',$this->getViewData());
    }

    public function download()
    {
        return \PDF::loadView('labels.chile.cn35.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        return \PDF::loadView('labels.chile.cn35.index',$this->getViewData())->save($path);
    }

    private function getViewData()
    {
        return [
            'companyName' => $this->companyName,
            'dispatchNumber' => $this->dispatchNumber,
            'serialNumber' => $this->serialNumber,
            'dispatchDate' => $this->dispatchDate,
            'originAirpot' => $this->originAirpot,
            'destinationAirport' => $this->destinationAirport,
            'itemsCount' => $this->itemsCount,
            'weight' => $this->weight,
            'service' => $this->service,
            'seal_no' => $this->seal_no,
            'bar_code' => $this->get_code_for_generating_barcode(),
            'awb' => $this->awb,
        ];
    }

}