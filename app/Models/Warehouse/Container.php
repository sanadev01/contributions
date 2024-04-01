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
use Spatie\Activitylog\LogOptions;
class Container extends Model implements \App\Services\Correios\Contracts\Container
{
    use SoftDeletes;

    protected $guarded = [];
    
    use LogsActivity;    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }

    const CONTAINER_ANJUN_NX = 'AJ-NX';
    const CONTAINER_ANJUN_IX = 'AJ-IX';
    const CONTAINER_ANJUNC_NX = 'AJC-NX';
    const CONTAINER_ANJUNC_IX = 'AJC-IX';
    const CONTAINER_BCN_NX = 'BCN-NX';
    const CONTAINER_BCN_IX = 'BCN-IX';
    const CONTAINER_COLOMBIA = 'CO-NX';

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

    public function getContainerTypeAttribute()
    {
        return $this->unit_type == 1 ? 'Bag' : 'Box';
    }

    public function getServiceSubclassNameAttribute()
    {
        return match ($this->services_subclass_code) {
            'NX' => 'Packet Standard service',
            'IX' => 'Packet Express service',
            'XP' => 'Packet Mini service',
            'SL-NX' => 'SL Standard Modal',
            'SL-IX' => 'SL Express Modal',
            'SL-XP' => 'SL Small Parcels',
            'AJ-NX' => 'AJ Packet Standard Service',
            'AJC-NX' => 'AJC Packet Standard Service',
            'AJC-IX' => 'AJC Packet Express Service',
            'AJ-IX' => 'AJ Packet Express service',
            'BCN-NX' => 'BCN Standard service',
            'BCN-IX' => 'BCN Express service',
            'SRM' => 'SRM service',
            'SRP' => 'SRP service',
            'Priority' => 'Priority',
            '537' => 'Global eParcel Prime',
            '773' => 'Prime5',
            'USPS Ground' => 'USPS Ground',
            '734' => 'Post Plus',
            '357' => 'Prime5RIO',
            ShippingService::GDE_PRIORITY_MAIL => 'GDE Priority Mail',
            ShippingService::GDE_FIRST_CLASS => 'GDE First Class',
            ShippingService::GSS_PMI => 'Priority Mail International',
            ShippingService::GSS_EPMEI => 'Priority Mail Express International (Pre-Sort)',
            ShippingService::GSS_EPMI => 'Priority Mail International (Pre-Sort)',
            ShippingService::GSS_FCM => 'First Class Package International',
            ShippingService::GSS_EMS => 'Priority Mail Express International (Nationwide)',
            ShippingService::GSS_CEP => 'GSS Commercial E-Packet',
            ShippingService::TOTAL_EXPRESS => 'Total Express',
            ShippingService::DirectLinkAustralia => 'DirectLink Australia',
            ShippingService::DirectLinkCanada => 'DirectLink Canada',
            ShippingService::DirectLinkMexico => 'DirectLink Mexico',
            ShippingService::DirectLinkChile => 'DirectLink Chile',
            default => 'FirstClass',
        };
    }

    public function getServiceCodeAttribute()
    {
        return match ($this->services_subclass_code) {
            'NX' => 2,
            'IX' => 1,
            'XP' => 3,
            'SRM' => 4,
            'SRP' => 5,
            'Priority' => 6,
            'FirstClass' => 7,
            'AJ-NX' => 8,
            'AJ-IX' => 9,
            '537' => 10,
            '773' => 11,
            '05' => 12,
            '734' => 13,
            ShippingService::GDE_PRIORITY_MAIL => 14,
            ShippingService::GDE_FIRST_CLASS => 15,
            ShippingService::TOTAL_EXPRESS => 16,
            ShippingService::HD_Express => 17,
            'AJC-IX' => 18,
            'AJC-NX' => 19,
            'BCN-NX' => 20,
            'BCN-IX' => 21,
            ShippingService::HoundExpress => 22,
            'CO-NX' => 23,
            default => null,
        };
    }

    public function getDestinationAriportAttribute()
    {
        return match ($this->destination_operator_name) {
            'SAOD' => 'GRU',
            'CRBA' => 'CWB',
            'MIA' => 'Miami',
            'MR' => 'Santiago',
            default => 'Other Region',
        };
    }

    public function getTotalWeightAttribute()
    {
        return (float) round($this->orders()->sum(DB::raw('CASE WHEN orders.measurement_unit = "kg/cm" THEN orders.weight ELSE ROUND((orders.weight/2.205), 2) END')),2);
    }

    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    public function getIsRegisteredAttribute()
    {
        return $this->unit_code?true:false;
    }

    public function getIsShippedAttribute()
    {
        return $this->deliveryBills()->count() > 0;
    }

    public function getSubclassCodeAttribute()
    {
        return match (true) {
            in_array($this->services_subclass_code, ['AJ-NX', 'BCN-NX', 'AJC-NX']) => 'NX',
            in_array($this->services_subclass_code, ['AJ-IX', 'BCN-IX', 'AJC-IX']) => 'IX',
            default => $this->services_subclass_code,
        };
    }

    public function getHasAnjunServiceAttribute()
    {
        return in_array($this->services_subclass_code ,['AJ-NX', 'AJ-IX']);
    } 
    public function getHasBcnServiceAttribute()
    {
        return in_array($this->services_subclass_code ,['BCN-NX', 'BCN-IX']);
    }
    public function getHasBcnStandardServiceAttribute()
    {
        return in_array($this->services_subclass_code ,['BCN-NX']);
    }
    public function getHasBcnExpressServiceAttribute()
    {
        return in_array($this->services_subclass_code ,['BCN-IX']);
        return $this->services_subclass_code == 'AJ-NX' || $this->services_subclass_code == 'AJ-IX';
    } 
    public function getHasAnjunChinaServiceAttribute()
    {  
        return in_array($this->services_subclass_code ,['AJC-NX','AJC-IX']);
    } 
    public function getHasAnjunChinaStandardServiceAttribute()
    {  
        return $this->services_subclass_code == 'AJC-NX';
    } 
    public function getHasAnjunChinaExpressServiceAttribute()
    {  
        return $this->services_subclass_code == 'AJC-IX';
    }
    public function getHasOrdersAttribute()
    {
        return $this->orders->isNotEmpty();
    }  

    public function getHasGepsServiceAttribute()
    {
        return in_array($this->services_subclass_code, [
            ShippingService::GePS,
            ShippingService::GePS_EFormat,
            ShippingService::Parcel_Post
        ]);
    }

    public function getHasSwedenPostServiceAttribute()
    {
        return in_array($this->services_subclass_code, [
            ShippingService::Prime5,
            ShippingService::Prime5RIO,
            ShippingService::DirectLinkCanada,
            ShippingService::DirectLinkMexico,
            ShippingService::DirectLinkChile,
            ShippingService::DirectLinkAustralia
        ]);
    }

    public function getIsDirectlinkCountryAttribute()
    {
        return in_array($this->services_subclass_code, [ 
            ShippingService::DirectLinkCanada,
            ShippingService::DirectLinkMexico,
            ShippingService::DirectLinkChile,
            ShippingService::DirectLinkAustralia
        ]);
    }

    public function getHasPostPlusServiceAttribute()
    {
        return in_array($this->services_subclass_code, [
            ShippingService::Post_Plus_Registered,
            ShippingService::Post_Plus_EMS,
            ShippingService::Post_Plus_Prime,
            ShippingService::Post_Plus_Premium
        ]);
    }

    public function getHasGdeServiceAttribute()
    {
        return in_array($this->services_subclass_code, [
            ShippingService::GDE_PRIORITY_MAIL,
            ShippingService::GDE_FIRST_CLASS
        ]);
    }

    public function getHasGssServiceAttribute()
    {
        return in_array($this->services_subclass_code, [
            ShippingService::GSS_PMI,
            ShippingService::GSS_EPMEI,
            ShippingService::GSS_EPMI,
            ShippingService::GSS_FCM,
            ShippingService::GSS_EMS,
            ShippingService::GSS_CEP
        ]);
    }
    public function getHasTotalExpressServiceAttribute()
    {
        return $this->services_subclass_code == ShippingService::TOTAL_EXPRESS;
    }
    public function getHasHoundExpressAttribute()
    {
        return $this->services_subclass_code == ShippingService::HoundExpress;
    }

    public function getHasHdExpressServiceAttribute()
    {
        return $this->services_subclass_code == ShippingService::HD_Express;
    }

    public function getGroup($container) {
        
        $containerOrder = $container->orders->first();
        $firstOrderGroupRange = getOrderGroupRange($containerOrder);
        
        return $firstOrderGroupRange['group'];
    }

    public function hasColombiaService()
    {
        return $this->services_subclass_code == 'CO-NX';
    }
}
