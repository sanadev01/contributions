<?php

namespace App\Services\PasarEx;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\ShippingService;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Services\Correios\Models\Package;
use App\Services\Correios\Contracts\HasLableExport;

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
    private $activeAddress;
    private $isReturn;
    private $packageSign;
    private $labelZipCodeGroup;

    public function __construct()
    {
        $this->hasSuplimentary = false;
        $this->hasAnjunLabel = false;
        $this->corriosLogo = \public_path('images/correios-1.png');
        $this->partnerLogo =  public_path('images/hd-label-logo-1.png');
        $this->packetType = 'Packet Standard';
        $this->contractNumber = 'H Contract:  9912501576';
        $this->packageSign = 'H';
        $this->service = 2;
        $this->returnAddress = 'Homedeliverybr <br>
        Rua Acaçá 47- Ipiranga <br>
        Sao Paulo CEP 04201-020';
        $this->complainAddress = 'Em caso de problemas com o produto, entre em contato com o remetente';
        $this->activeAddress = '';
        $this->labelZipCodeGroup = '';
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
        $this->recipient = $order->recipient;
        $this->order->load('items');
        $this->setItems()->setSuplimentryItems();
        $this->getActiveAddress($this->order);
        $this->checkReturn($this->order);
        if(optional($this->order->order_date)->greaterThanOrEqualTo(Carbon::parse('2024-01-01'))) {
            $this->labelZipCodeGroup = getOrderGroupRange($this->order);
        }  
        $this->contractNumber = 'Contract: 9912501700';
        $this->packageSign = ''; 
        $this->packetType = 'Pasar Ex';
        $this->serviceLogo = public_path('images/pasarex_logo.png'); 
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
        if ($this->order->items->count() > $suplimentryAt) {
            $this->hasSuplimentary = true;
            $this->sumplementryItems = $this->order->items->skip($suplimentryAt)->chunk(30);
        }
        return $this;
    }
    private function suplimentryAt()
    {
        $suplimentryAt = 4;
        foreach ($this->order->items as  $key => $item) {
            if (strlen($item->description) > 65) {
                try {

                    if (strlen($this->order->items[$key + 1]->description) <= 70  && $key < 2)
                        $suplimentryAt = $key == 0 ? 2 : $key + 1;
                    else
                        $suplimentryAt = $key == 0 ? 1 : $key;
                } catch (Exception $e) {
                    $suplimentryAt = $key == 0 ? 1 : $key;
                }
            }
        }
        return $suplimentryAt > 4 ? 4 : $suplimentryAt;
    }

    public function render()
    {
        return view('labels.pasarex.cn23.index', $this->getViewData());
    }

    public function download()
    {
        if (!$this->order) {
            throw new Exception("Order not Set");
        }

        return \PDF::loadView('labels.pasarex.cn23.index', $this->getViewData())->stream();
    }

    public function saveAs($path)
    {
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }
        return \PDF::loadView('labels.pasarex.cn23.index', $this->getViewData())->save($path);
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
            'activeAddress' => $this->activeAddress,
            'isReturn' => $this->isReturn,
            'labelZipCodeGroup' => $this->labelZipCodeGroup,
            'packageSign' => $this->packageSign
        ];
    }

    private function getActiveAddress(Order $order)
    {
        if (setting('default_address', null, $order->user_id) == 3) {
            $user = $order->user;
            $this->activeAddress = $user->address . ', ' . $user->state->code . ', ' . $user->zipcode . ', ' . $user->country()->first()->code;
        } else {
            $this->activeAddress = "2200 NW 129th Ave - Suite # 100, Miami, FL 33182 US";
        }
        return $this;
    }

    private function checkReturn(Order $order)
    {
        if ($order->sinerlog_tran_id) {
            $this->isReturn = false;
            if ($order->sinerlog_tran_id == 1  || $order->sinerlog_tran_id == 3) {
                $this->isReturn = true;
            }
        } else {
            // $id = auth()->user()->id;
            $this->isReturn = true;
            // if(setting('return_origin', null, $id) || setting('individual_parcel', null, $id)) {
            //     $this->isReturn = true;
            // }
        }
        return $this;
    }
}
