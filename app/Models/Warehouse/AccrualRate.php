<?php

namespace App\Models\Warehouse;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use App\Services\Correios\Models\Package;
use App\Services\Converters\UnitsConverter;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class AccrualRate extends Model
{
    use LogsActivity;
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    
    public function getServiceName()
    {
        if ( $this->service == Package::SERVICE_CLASS_STANDARD ){
            return "Standard";
        }

        if ( $this->service == Package::SERVICE_CLASS_EXPRESS ){
            return "Express";
        }

        if ( $this->service == Package::SERVICE_CLASS_MINI ){
            return "Mini";
        }

        if ( $this->service == Package::SERVICE_CLASS_SRP ){
            return "SRP";
        }

        if ( $this->service == Package::SERVICE_CLASS_SRM ){
            return "SRM";
        }

        if ( $this->service == Package::SERVICE_CLASS_AJ_Standard ){
            return "Anjun Standard";
        }

        if ( $this->service == Package::SERVICE_CLASS_AJ_EXPRESS ){
            return "Anjun Express";
        }
        
        if ( $this->service == Package::SERVICE_CLASS_GePS ){
            return "Global eParcel Prime";
        }

        if ( $this->service == Package::SERVICE_CLASS_GePS_EFormat ){
            return "Global eParcel Untracked Packet";
        }

        if ( $this->service == Package::SERVICE_CLASS_Prime5 ){
            return "Prime5";
        }

        if ( $this->service == Package::SERVICE_CLASS_Post_Plus_Registered ){
            return "Post Plus Registered";
        }
        if ( $this->service == Package::SERVICE_CLASS_Post_Plus_EMS ){
            return "Post Plus EMS";
        }

        if ( $this->service == Package::SERVICE_CLASS_Parcel_Post ){
            return "Parcel Post";
        }

        if ( $this->service == Package::SERVICE_CLASS_Post_Plus_Prime ){
            return "Post Plus Prime";
        }

        if ( $this->service == Package::SERVICE_CLASS_Post_Plus_Premium){
            return "PrimeRIO";
        }

        if ( $this->service == Package::SERVICE_CLASS_Prime5RIO ){
            return "Prime5RIO";
        }

        if ( $this->service == Package::SERVICE_CLASS_GDE_PRIORITY ){
            return "GDE Priority Mail";
        }
        if ( $this->service == Package::SERVICE_CLASS_GDE_FIRSTCLASS ){
            return "GDE First Class";
        }
        if ( $this->service == Package::SERVICE_CLASS_TOTAL_EXPRESS ){
            return "Total Express";
        }
        if ( $this->service == Package::SERVICE_CLASS_LT_PRIME ){
            return "Prime LT";
        }
        if ( $this->service == Package::SERVICE_CLASS_Post_Plus_LT_Premium ){
            return "PostPlus Portugal";
        }
        if ( $this->service == Package::SERVICE_CLASS_Post_Plus_CO_EMS ){
            return "PostPlus Colombia EMS";
        }
        if ( $this->service == Package::SERVICE_CLASS_Post_Plus_CO_REG ){
            return "PostPlus Colombia REG";
        }
        if ( $this->service == Package::SERVICE_CLASS_Japan_Prime ){
            return "Japan JerseyPost Prime";
        }
        if ( $this->service == Package::SERVICE_CLASS_Japan_EMS ){
            return "Japan JerseyPost EMS";
        }
        return '';
    }

    public static function getRateSlabFor($weight, $service  = null): AccrualRate
    {
        if($weight < 0.1){
            $weight = 0.1;
        }
        $weightToGrams = UnitsConverter::kgToGrams($weight);

        return self::where('weight','<=',$weightToGrams)->where('service',$service)->orderBy('id','DESC')->take(1)->first();
    }

    public static function getCarrierRate($weight, $service)
    {
        if($weight < 0.1){
            $weight = 0.1;
        }

        $weightToGrams = UnitsConverter::kgToGrams($weight);

        return self::where([
            ['weight','<=',$weightToGrams],
            ['service', $service]
        ])->orderBy('id','DESC')->take(1)->first();
    }

    
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
