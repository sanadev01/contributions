<?php

namespace App\Models\Warehouse;

use Carbon\Carbon;
use App\Models\User;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class DeliveryBill extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    protected $guarded = [];

    public function containers()
    {
        return $this->belongsToMany(Container::class);
    }

    public function isRegistered()
    {
        return $this->request_id;
    }

    public function isReady()
    {
        return $this->cnd38_code;
    }

    public function isRequestPlaced()
    {
        return $this->request_id;
    }

    public function getWeight()
    {
        $weight = 0;
        foreach ($this->containers as $container){
            $weight += round($container->orders()->sum(DB::raw('CASE WHEN orders.measurement_unit = "kg/cm" THEN orders.weight ELSE (orders.weight/2.205) END')),2);
        }

        return $weight;
    }

    /**
     * generate random cnd38_code
     * @return string
     */
    public function setCN38Code()
    {
        return $date = date('idmy', strtotime(Carbon::now()));
    }

    /**
     * generate random string
     * @return string
     */
    public function setRandomRequestId()
    {
        return str_random(8).'-'.str_random(4).'-'.str_random(4).'-'.str_random(4).'-'.str_random(12);
    }

    public function isGePS()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::GePS){
            return true;
        }
    }

    public function isSwedenPost()
    {
        if($this->containers->first()->is_directlink_country ||$this->containers->first()->services_subclass_code == ShippingService::Prime5 || $this->containers->first()->services_subclass_code == ShippingService::Prime5RIO){
            return true;
        }
    }

    public function isPostPlus()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::Post_Plus_Registered){
            return true;
        }
    }

    public function isGDE()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::GDE_PRIORITY_MAIL || $this->containers->first()->services_subclass_code == ShippingService::GDE_FIRST_CLASS){
            return true;
        }
        return false;
    }

    public function isGSS()
    {
        if(($this->containers->first()->services_subclass_code == ShippingService::GSS_PMI) || ($this->containers->first()->services_subclass_code == ShippingService::GSS_EPMEI) || ($this->containers->first()->services_subclass_code == ShippingService::GSS_EPMI) || ($this->containers->first()->services_subclass_code == ShippingService::GSS_FCM) || ($this->containers->first()->services_subclass_code == ShippingService::GSS_EMS) || ($this->containers->first()->services_subclass_code == ShippingService::GSS_CEP)){
            return true;
        }
    }

    public function containerShippingService($subService)
    {
       return $this->containers->first()->services_subclass_code == $subService;
    }

    public function isHDExpress()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::HD_Express){
            return true;
        }
        return false;
    }

    public function isHoundExpress()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::HoundExpress){
            return true;
        }
        return false;
    }

    public function isTotalExpress()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::TOTAL_EXPRESS){
            return true;
        }
        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isAnjunChina()
    {
        return $this->containers->first()->hasAnjunChinaService();
    }
    public function isBCN()
    {
        return $this->containers->first()->hasBCNService();
    }
    public function isPasarEx()
    {
        return $this->containers->first()->hasPasarExService();
    }

}
