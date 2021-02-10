<?php

namespace App\Models\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeliveryBill extends Model
{
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

}
