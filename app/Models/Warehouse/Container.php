<?php

namespace App\Models\Warehouse;

use App\Models\User;
use App\Models\Order;
use App\Models\ShippingService;
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

    const CONTAINER_ANJUN_NX = 'AJ-NX';
    const CONTAINER_ANJUN_IX = 'AJ-IX';
         const CONTAINER_MILE_EXPRESS = 'ML-EX';
    const CONTAINER_COLOMBIA = 'CO-NX';
    const CONTAINER_ANJUNC_NX = 'AJC-NX';
    const CONTAINER_ANJUNC_IX = 'AJC-IX';


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
        }elseif($this->services_subclass_code == 'XP'){
            return 'Packet Mini service';
        }elseif($this->services_subclass_code == 'SL-NX'){
            return 'SL Standard Modal';
        }elseif($this->services_subclass_code == 'SL-IX'){
            return 'SL Express Modal';
        }elseif($this->services_subclass_code == 'SL-XP'){
            return 'SL Small Parcels';
        }elseif ($this->services_subclass_code == 'AJ-NX') {
            return 'AJ Packet Standard service';
        }elseif ($this->services_subclass_code == 'AJC-NX') {
            return 'China AJ Packet Standard service';
        }elseif ($this->services_subclass_code == 'AJC-IX') {
            return 'China AJ Packet Express service';
        }elseif ($this->services_subclass_code == 'AJ-IX') {
            return 'AJ Packet Express service';
        }elseif ($this->services_subclass_code == 'ML-EX') {
            return 'Mile Express';
        }elseif($this->services_subclass_code == 'SRM'){
            return 'SRM service';
        }elseif($this->services_subclass_code == 'SRP'){
            return 'SRP service';
        }elseif($this->services_subclass_code == 'CO-NX'){
            return 'Colombia Service';
        }elseif($this->services_subclass_code == 'PostNL'){
            return 'PostNL';
        }elseif($this->services_subclass_code == '537'){
            return 'GePS';
        }elseif($this->services_subclass_code == 'Priority'){
            return 'Priority';
        }elseif($this->services_subclass_code == '537'){
            return 'Global eParcel Prime';
        }elseif($this->services_subclass_code == '773'){
            return 'Prime5';
        }elseif($this->services_subclass_code == 'USPS Ground'){
            return 'USPS Ground';
        }elseif($this->services_subclass_code == '734'){
            return 'Post Plus';
        }elseif($this->services_subclass_code == 'Priority International'){
            return 'Priority International';
        }elseif($this->services_subclass_code == 'FirstClass International'){
            return 'FirstClass International';
        }elseif($this->services_subclass_code == 'FirstClass International'){
            return 'USPS Ground';
        }else {
            return 'FirstClass';
        }
    }

    public function getServiceCode()
    {
        if($this->services_subclass_code == 'NX'){
            return  2;
        }elseif($this->services_subclass_code == 'IX'){
            return 1;
        }elseif($this->services_subclass_code == 'XP'){
            return 3;
        }elseif($this->services_subclass_code == 'SRM') {
            return 4;
        }elseif($this->services_subclass_code == 'SRP') {
            return 5;
        }elseif($this->services_subclass_code == 'Priority') {
            return 6;
        }elseif($this->services_subclass_code == 'FirstClass'){
            return 7;
        }elseif($this->services_subclass_code == 'AJC-NX') {
            return 8;
        }elseif($this->services_subclass_code == 'AJ-IX'){
            return 9;
        }elseif($this->services_subclass_code == 'CO-NX'){
            return 10;
        }elseif($this->services_subclass_code == 'Priority International'){
            return 11;
        }elseif($this->services_subclass_code == 'FirstClass International'){
            return 12;
        }elseif($this->services_subclass_code == 'PostNL'){
            return 13;
        }elseif($this->services_subclass_code == '537'){
            return 14;
        }elseif ($this->services_subclass_code == 'ML-EX') {
            return 15;
        }
        elseif($this->services_subclass_code == '773'){
            return 11;
        }
        elseif($this->services_subclass_code == '734'){
            return 12;
        }
        elseif($this->services_subclass_code == 'CO-NX'){
            return 12;
        }
        elseif($this->services_subclass_code == 'USPS Ground'){
            return 7;
        }
        elseif( $this->services_subclass_code == 'AJC-IX') {
            return 14;
        } 
        // return $this->services_subclass_code == 'NX' ? 2 : 1;
    }

    public function getDestinationAriport()
    {
        if($this->destination_operator_name == 'SAOD'){
            return 'GRU';
        }elseif($this->destination_operator_name == 'CRBA') {
            return 'CWB';
        }elseif($this->destination_operator_name == 'MIA') {
            return 'Miami';
        }elseif($this->destination_operator_name == 'MR') {
            return 'Santiago';
        }else {
            return 'Other Region';
        }
        // return $this->destination_operator_name == 'SAOD' ? 'GRU' : 'CWB';
    }

    public function getWeight(): float
    {
        return round($this->orders()->sum(DB::raw('CASE WHEN orders.measurement_unit = "kg/cm" THEN orders.weight ELSE ROUND((orders.weight/2.205), 2) END')),2);
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

    public function getSubClassCode()
    {
        if ($this->services_subclass_code == 'AJ-NX' ||$this->services_subclass_code == 'AJC-NX'){
            return 'NX';
        }

        if ($this->services_subclass_code == 'AJ-IX' ||$this->services_subclass_code == 'AJC-IX'){
            return 'IX';
        }

        if ($this->services_subclass_code == '537') {
            return 'IX';
        }

        return $this->services_subclass_code;
    }

    public function hasAnjunService()
    {  
        return $this->services_subclass_code == 'AJ-NX' || $this->services_subclass_code == 'AJ-IX';
    } 
    public function hasAnjunChinaService()
    {  
        return $this->services_subclass_code == 'AJC-NX' || $this->services_subclass_code == 'AJC-IX';
    } 
    public function hasAnjunChinaStandardService()
    {  
        return $this->services_subclass_code == 'AJC-NX';
    } 
    public function hasAnjunChinaExpressService()
    {  
        return $this->services_subclass_code == 'AJC-IX';
    }
    public function hasOrders()
    {
        return $this->orders->isNotEmpty();
    }

    public function hasGePSService()
    {
        return $this->services_subclass_code == ShippingService::GePS || $this->services_subclass_code == ShippingService::GePS_EFormat || $this->services_subclass_code == ShippingService::Parcel_Post;
    }

    public function hasSwedenPostService()
    {
        return $this->services_subclass_code == ShippingService::Prime5;
    }

    public function hasPostPlusService()
    {
        return $this->services_subclass_code == ShippingService::Post_Plus_Registered || $this->services_subclass_code == ShippingService::Post_Plus_EMS || $this->services_subclass_code == ShippingService::Post_Plus_Prime || $this->services_subclass_code == ShippingService::Post_Plus_Premium;
    }

    public function hasGDEService()
    {
        return $this->services_subclass_code == ShippingService::GDE_Service;
    }
    
    public function hasColombiaService()
    {
        return $this->services_subclass_code == 'CO-NX';
    }

    
    public function getContainerService()
    {
        if ($this->services_subclass_code == 'NX' || $this->services_subclass_code == 'IX' || $this->services_subclass_code == 'XP') {
            return 'Brazil-Container';
        }

        if ($this->services_subclass_code == 'AJ-NX' || $this->services_subclass_code == 'AJ-IX') {
            return 'Anjun-Container';
        }

        if ($this->services_subclass_code == 'SL-NX' || $this->services_subclass_code == 'SL-IX' || $this->services_subclass_code == 'SL-XP') {
            return 'Sinerlog-Container';
        }

        if ($this->services_subclass_code == 'SRM' || $this->services_subclass_code == 'SRP') {
            return 'Chile-Container';
        }

        if ($this->services_subclass_code == 'Priority' || $this->services_subclass_code == 'FirstClass' ||
            $this->services_subclass_code == 'Priority International' || $this->services_subclass_code == 'FirstClass International' || $this->services_subclass_code == 'FirstClass' || $this->services_subclass_code == 'USPS Ground') {
            return 'USPS-Container';
        }

        if ($this->services_subclass_code == 'ML-EX') {
            return 'MileExpress-Container';
        }
        
        if ($this->services_subclass_code == 'CO-NX') {
            return 'Colombia-Container';
        }
        if ($this->services_subclass_code == 'PostNL') {
            return 'PostNL';
        }

        if ($this->services_subclass_code == '537') {
            return 'GePS';
        }

        return 'Other-Container';
    }
}
