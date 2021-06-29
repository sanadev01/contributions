<?php

namespace App\Models\Warehouse;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Resources\Warehouse\Container\PackageResource;

class Container extends Model implements \App\Services\Correios\Contracts\Container
{
    use SoftDeletes;

    protected $guarded = [];
    
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRegistered(Builder $builder)
    {
        return $builder->whereNotNull('unit_code');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function deliveryBills()
    {
        return $this->belongsToMany(DeliveryBill::class);
    }

    public function getOrdersCollections()
    {
        return PackageResource::collection($this->orders);
    }

    public function getContainerType()
    {
        return $this->unit_type == 1 ? 'Bag' : 'Box';
    }

    public function getServiceSubClass()
    {
        if($this->services_subclass_code == 'NX'){
            return  'Packet Standard service';
        }elseif($this->services_subclass_code == 'IX'){
            return 'Packet Express service';
        }else{
            return 'Packet Mini service';
        }
    }

    public function getServiceCode()
    {
        if($this->services_subclass_code == 'NX'){
            return  2;
        }elseif($this->services_subclass_code == 'IX'){
            return 1;
        }else{
            return 3;
        }
        // return $this->services_subclass_code == 'NX' ? 2 : 1;
    }

    public function getDestinationAriport()
    {
        return $this->destination_operator_name == 'SAOD' ? 'GRU' : 'CWB';
    }

    public function getWeight(): float
    {
        return round($this->orders()->sum(DB::raw('CASE WHEN orders.measurement_unit = "kg/cm" THEN orders.weight ELSE (orders.weight/2.205) END')),2);
    }

    public function getPiecesCount(): int
    {
        return $this->orders()->count();
    }

    public function getUnitCode()
    {
        return $this->unit_code;
    }

    public function isRegistered()
    {
        return $this->unit_code;
    }

    public function isShipped()
    {
        return $this->deliveryBills()->count() > 0;
    }
}
