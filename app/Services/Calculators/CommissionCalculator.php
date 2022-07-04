<?php

namespace App\Services\Calculators;

use App\Models\Order;
use App\Models\AffiliateSale;
use App\Models\CommissionSetting;


class CommissionCalculator
{
    private $order;
    private $shippingCost;
    private $user;
    private $referrer;

    public function __construct(Order $order)
    {
        $this->order        = $order;
        $this->shippingCost = $order->shipping_value;
        $this->user         = $order->user;
        $this->referrer     = $this->user->referrer;
        
    }

    public function hasReferrer()
    {
        return $this->referrer;
    }
    
    public function getCommissionSetting()
    {
        if(!$this->hasReferrer()){
            return null;
        }

        $setting = CommissionSetting::where('user_id', $this->referrer->id)->where('referrer_id', $this->user->id)->first();
        if($setting){
            return $setting;
        }

        return $this->getAdminCommissionSetting();

    }

    public function getCommission()
    {
        $affiliatCommissionSetting = $this->getCommissionSetting();
        
        if($affiliatCommissionSetting){
            if( $this->isFlat() ){
                return $affiliatCommissionSetting->value;
            }
            return $this->shippingCost * $affiliatCommissionSetting->value / 100;
        }

        return 0;

    }

    public function getValue()
    {
        return $this->getCommissionSetting()? $this->getCommissionSetting()->value : $this->getAdminCommissionSetting()->value;
    }
    
    public function isFlat()
    {
        return $this->getCommissionSetting()->type == 'flat' ? true : false;
    }

    public function isPercentage()
    {
        return $this->getCommissionSetting()->type == 'percentage' ? true : false;
    }

    public function getAdminCommissionSetting()
    {
        return $data =(object) [
            'value' => setting('VALUE',$default = null, $userId = null, $admin = true),
            'type' => setting('TYPE',$default = null, $userId = null, $admin = true),
        ];

    }
}
