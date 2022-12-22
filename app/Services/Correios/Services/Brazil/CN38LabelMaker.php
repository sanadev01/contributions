<?php

namespace App\Services\Correios\Services\Brazil;

use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;
use App\Services\Correios\Contracts\HasLableExport;
use PDF;

class CN38LabelMaker implements HasLableExport{


    private $logo;
    private $deliveryBill;
    private $deliveryBillNo;
    private $officeOfOrigin;
    private $contractNo;
    private $airLine;
    private $flightNo;
    private $date;
    private $time;
    private $departureDate;
    private $service;
    private $taxModality;
    private $originAirpot;
    private $destinationAirpot;
    private $dispatchData;
    private $containers;

    // Table


    public function __construct()
    {
        $this->logo = public_path('images/hd-1cm.png');
        $this->originLogo = public_path('images/hd-1cm.png');
        $this->deliveryBillNo = '';
        $this->officeOfOrigin = public_path('images/hd-1cm.png');
        $this->contractNo = '9912501576';
        $this->airLine = '';
        $this->flightNo = '';
        $this->date = '';
        $this->time = '';
        $this->departureDate = '';
        $this->service = '';
        $this->originAirpot = '';
        $this->destinationAirpot = '';
        $this->dispatchData = '';
        $this->containers = collect()->times(15,function(){
            return new Container();
        });
    }

    /**
     * Set the value of logo
     *
     * @return  self
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    public function setOriginLogo($originLogo)
    {
        $this->originLogo = $originLogo;

        return $this;
    }

    public function setDeliveryBill(DeliveryBill $deliveryBill)
    {
        $this->deliveryBill = $deliveryBill;
    }

    /**
     * Set the value of deliveryBillNo
     *
     * @return  self
     */
    public function setDeliveryBillNo($deliveryBillNo)
    {
        $this->deliveryBillNo = $deliveryBillNo;

        return $this;
    }

    /**
     * Set the value of officeOfOrigin
     *
     * @return  self
     */
    public function setOfficeOfOrigin($officeOfOrigin)
    {
        $this->officeOfOrigin = $officeOfOrigin;

        return $this;
    }

    /**
     * Set the value of contractNo
     *
     * @return  self
     */
    public function setContractNo($contractNo)
    {
        $this->contractNo = $contractNo;

        return $this;
    }

    /**
     * Set the value of airLine
     *
     * @return  self
     */
    public function setAirLine($airLine)
    {
        $this->airLine = $airLine;

        return $this;
    }

    /**
     * Set the value of flightNo
     *
     * @return  self
     */
    public function setFlightNo($flightNo)
    {
        $this->flightNo = $flightNo;

        return $this;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Set the value of time
     *
     * @return  self
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Set the value of departureDate
     *
     * @return  self
     */
    public function setDepartureDate($departureDate)
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    /**
     * Set the value of service
     *
     * @return  self
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

     /**
     * Set the value of taxModality
     *
     * @return  self
     */
    public function setTaxModality($taxModality)
    {
        $this->taxModality = $taxModality;

        return $this;
    }

    /**
     * Set the value of originAirpot
     *
     * @return  self
     */
    public function setOriginAirpot($originAirpot)
    {
        $this->originAirpot = $originAirpot;

        return $this;
    }

    /**
     * Set the value of destinationAirpot
     *
     * @return  self
     */
    public function setDestinationAirpot($destinationAirpot)
    {
        $this->destinationAirpot = $destinationAirpot;

        return $this;
    }

    /**
     * Set the value of dispatchData
     *
     * @return  self
     */
    public function setDispatchData($dispatchData)
    {
        $this->dispatchData = $dispatchData;

        return $this;
    }

    /**
     * Set the value of bags
     *
     * @return  self
     */
    public function setBags($bags)
    {
        $this->containers = $bags;

        return $this;
    }

    public function render()
    {
        return view('labels.brazil.cn38.index',$this->getViewData());
    }

    public function download()
    {
        return PDF::loadView('labels.brazil.cn38.index',$this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        return PDF::loadView('labels.brazil.cn38.index',$this->getViewData())->save($path);
    }

    private function getViewData()
    {
        return [
            'logo' => $this->logo,
            'originLogo' => $this->originLogo,
            'deliveryBill' => $this->deliveryBill,
            'deliveryBillNo' => $this->deliveryBillNo,
            'officeOfOrigin' => $this->officeOfOrigin,
            'contractNo' => $this->contractNo,
            'airLine' => $this->airLine,
            'flightNo' => $this->flightNo,
            'date' => $this->date,
            'time' => $this->time,
            'departureDate' => $this->departureDate,
            'service' => $this->service,
            'taxModality' => $this->taxModality,
            'originAirpot' => $this->originAirpot,
            'destinationAirpot' => $this->destinationAirpot,
            'dispatchData' => $this->dispatchData,
            'containers' => $this->containers,
        ];
    }

}
