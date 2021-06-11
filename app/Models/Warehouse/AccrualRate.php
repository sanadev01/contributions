<?php

namespace App\Models\Warehouse;

use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Models\Package;
use Illuminate\Database\Eloquent\Model;

class AccrualRate extends Model
{

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

        return '';
    }

    public static function getRateSlabFor($weight): AccrualRate
    {
        if($weight < 0.1){
            $weight = 0.1;
        }
        $weightToGrams = UnitsConverter::kgToGrams($weight);

        return self::where('weight','<=',$weightToGrams)->orderBy('id','DESC')->take(1)->first();
    }
}
