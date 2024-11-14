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
use App\Services\Correios\GetZipcodeGroup;

class Container extends Model implements \App\Services\Correios\Contracts\Container
{
    use SoftDeletes;

    protected $guarded = [];
    
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;

    const CONTAINER_ANJUN_NX  = 'AJ-NX';
    const CONTAINER_ANJUN_IX  = 'AJ-IX';
    const CONTAINER_BCN_NX    = 'BCN-NX';
    const CONTAINER_BCN_IX    = 'BCN-IX';
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
        $serviceSubclasses = [
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
            ShippingService::TOTAL_EXPRESS => 'Total Express',
            ShippingService::DirectLinkAustralia => 'DirectLink Australia',
            ShippingService::DirectLinkCanada => 'DirectLink Canada',
            ShippingService::DirectLinkMexico => 'DirectLink Mexico',
            ShippingService::DirectLinkChile => 'DirectLink Chile',
            ShippingService::GSS_CEP => 'GSS Commercial E-Packet',
            ShippingService::PasarEx => 'PasarEx',
            ShippingService::DSS_SENEGAL => 'DSS Senegal',
            ShippingService::FOX_ST_COURIER => 'Fox Standard',
            ShippingService::FOX_EX_COURIER => 'Fox Express',
            ShippingService::DSS_SENEGAL=>'DSS Senegal',
            ShippingService::VIP_PARCEL_FCP=>'VIP Parcel First Class',
            ShippingService::VIP_PARCEL_PMEI=>'VIP Parcel Priority Mail Express International',
            ShippingService::VIP_PARCEL_PMI=>'VIP Parcel Priority Mail International',

            ShippingService::Cainiao=>'Cainiao',
            ShippingService::PHX_ST_COURIER => 'Phx Standard',
            ShippingService::PHX_EX_COURIER => 'Phx Express',
        ];
    
        // Check if the service subclass code exists in the array
        // If it does, return its corresponding description
        if (isset($serviceSubclasses[$this->services_subclass_code])) {
            return $serviceSubclasses[$this->services_subclass_code];
        } else {
            return 'FirstClass';
        }
    } 

    public function getServiceCode()
    {
        $serviceCodes = [
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
            ShippingService::PasarEx => 23,
            ShippingService::DSS_SENEGAL => 24,
            ShippingService::Cainiao => 25,
            ShippingService::FOX_ST_COURIER => 26,
            ShippingService::FOX_EX_COURIER => 27,
            ShippingService::PHX_ST_COURIER => 28,
            ShippingService::PHX_EX_COURIER => 29,
        ];
    
        // Check if the service subclass code exists in the array
        // If it does, return its corresponding code
        if (isset($serviceCodes[$this->services_subclass_code])) {
            return $serviceCodes[$this->services_subclass_code];
        } else {
            return null;
        }
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
        }elseif($this->destination_operator_name == 'BOG') {
            return 'BOG';
        }else {
            return 'Other Region';
        }
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
        if(in_array($this->services_subclass_code, ['AJ-NX' , 'BCN-NX','AJC-NX'])){
            return 'NX';
        }
        if(in_array($this->services_subclass_code , ['AJ-IX', 'BCN-IX','AJC-IX'])){
            return 'IX';
        }
        return $this->services_subclass_code;
    }

    public function hasAnjunService()
    {
        return in_array($this->services_subclass_code ,['AJ-NX', 'AJ-IX']);
    } 
    public function getHasCainiaoAttribute()
    {
        return in_array($this->services_subclass_code ,['1000']);
    } 
    public function hasBCNService()
    {
        return in_array($this->services_subclass_code ,['BCN-NX', 'BCN-IX']);
    }
    public function hasBCNStandardService()
    {
        return in_array($this->services_subclass_code ,['BCN-NX']);
    }
    public function hasBCNExpressService()
    {
        return in_array($this->services_subclass_code ,['BCN-IX']);
        return $this->services_subclass_code == 'AJ-NX' || $this->services_subclass_code == 'AJ-IX';
    } 
    public function hasAnjunChinaService()
    {  
        return in_array($this->services_subclass_code ,['AJC-NX','AJC-IX']);
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
        return in_array($this->services_subclass_code,[ShippingService::Prime5,ShippingService::Prime5RIO,ShippingService::DirectLinkCanada,ShippingService::DirectLinkMexico,ShippingService::DirectLinkChile,ShippingService::DirectLinkAustralia]);
    }
    function getIsDirectlinkCountryAttribute(){
        return in_array($this->services_subclass_code,[ShippingService::DirectLinkCanada,ShippingService::DirectLinkMexico,ShippingService::DirectLinkChile,ShippingService::DirectLinkAustralia]);
    }

    public function hasPostPlusService()
    {
        return $this->services_subclass_code == ShippingService::Post_Plus_Registered || $this->services_subclass_code == ShippingService::Post_Plus_EMS || $this->services_subclass_code == ShippingService::Post_Plus_Prime || $this->services_subclass_code == ShippingService::Post_Plus_Premium;
    }

    public function hasGDEService()
    {
        return $this->services_subclass_code == ShippingService::GDE_PRIORITY_MAIL || $this->services_subclass_code == ShippingService::GDE_FIRST_CLASS;
    }

    public function hasGSSService()
    {
        return $this->services_subclass_code == ShippingService::GSS_PMI || $this->services_subclass_code == ShippingService::GSS_EPMEI || $this->services_subclass_code == ShippingService::GSS_EPMI || $this->services_subclass_code == ShippingService::GSS_FCM || $this->services_subclass_code == ShippingService::GSS_EMS || $this->services_subclass_code == ShippingService::GSS_CEP;
    }

    public function getHasTotalExpressServiceAttribute()
    {
        return $this->services_subclass_code == ShippingService::TOTAL_EXPRESS || $this->services_subclass_code == ShippingService::TOTAL_EXPRESS_10KG;
    }
    public function getHasHoundExpressAttribute()
    {
        return $this->services_subclass_code == ShippingService::HoundExpress;
    }

    public function hasHDExpressService()
    {
        return $this->services_subclass_code == ShippingService::HD_Express;
    } 
    public function hasPasarExService()
    {
        return $this->services_subclass_code == ShippingService::PasarEx;
    }

    public function getGroup($container) {
        
        $firstOrder = $container->orders->first();
        return (new GetZipcodeGroup($firstOrder->recipient->zipcode))->getZipcodeGroup();
    }

    public function hasSenegalService()
    {
        return $this->services_subclass_code == ShippingService::DSS_SENEGAL;
    }

    public function getCustomType()
    {
        return ($this->custom_type == 1 || is_null($this->custom_type) || $this->custom_type === '') ? 'Non-PRC' : 'PRC';
    }

    public function isPRC()
    {
        return $this->custom_type == 2;
    }

    public function isPRCRegistered()
    {
        return !empty($this->customs_response_list);
    }

    public function hasFoxCourierService()
    {
        return $this->services_subclass_code == ShippingService::FOX_ST_COURIER || $this->services_subclass_code == ShippingService::FOX_EX_COURIER;

    }

    public function hasPhxCourierService()
    {
        return $this->services_subclass_code == ShippingService::PHX_ST_COURIER || $this->services_subclass_code == ShippingService::PHX_EX_COURIER;

    }
    public function hasVipParcelService()
    {
        return $this->services_subclass_code == ShippingService::VIP_PARCEL_FCP || $this->services_subclass_code == ShippingService::VIP_PARCEL_PMEI || $this->services_subclass_code == ShippingService::VIP_PARCEL_PMI;
    }

}
