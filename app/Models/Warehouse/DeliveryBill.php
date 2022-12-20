<?php

namespace App\Models\Warehouse;

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
    public function setRandomCN38Code()
    {
        return $this->id.random_int(1000, 9999);
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

    public function isDirectLink()
    {
        if($this->containers->first()->services_subclass_code == ShippingService::Direct_Link){
            return true;
        }
    }

}
