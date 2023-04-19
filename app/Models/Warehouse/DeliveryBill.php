<?php

namespace App\Models\Warehouse;

use Carbon\Carbon;
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

    /**
     * @return Container
     */
    public function container()
    {
        return $this->containers->first();
    }

    public function isPostNL()
    {
        if($this->containers->first()->services_subclass_code == 'PostNL'){
            return true;
        }
    }

    /**
     * @return bool
     */
    public function hasMileExpressService()
    {
        if ($this->container()->services_subclass_code == Container::CONTAINER_MILE_EXPRESS) {
            return true;
        }

        return false;
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
    
    public function isAnjunChinaStandard()
    {
        if($this->containers->first()->services_subclass_code == Container::CONTAINER_ANJUNC_NX){
            return true;
        }
    }
    
    public function isAnjunChinaExpress()
    {
        if($this->containers->first()->services_subclass_code == Container::CONTAINER_ANJUNC_IX){
            return true;
        }
    }

    public function isSwedenPost()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::Prime5 || $this->containers->first()->services_subclass_code == ShippingService::Prime5RIO){
            return true;
        }
    }

    public function isPostPlus()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::Post_Plus_Registered){
            return true;
        }
    }

    /**
     * @return bool
     */
    public function hasColombiaService()
    {
        if ($this->containers->first()->services_subclass_code == 'CO-NX') {
            return true;
        }

        return false;
    }
}
