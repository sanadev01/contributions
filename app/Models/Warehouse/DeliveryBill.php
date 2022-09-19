<?php

namespace App\Models\Warehouse;

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
    public function hasColombiaService()
    {
        if ($this->container()->services_subclass_code == Container::CONTAINER_COLOMBIA) {
            return true;
        }

        return false;
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

    public function isGePS()
    {
        if($this->containers->first()->services_subclass_code == '537'){
            return true;
        }
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
        return random_int(100000, 999999).'-'.random_int(1000, 9999).'-'.random_int(100000, 999999);
    }
}
