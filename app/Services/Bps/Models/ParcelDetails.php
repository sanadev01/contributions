<?php 

namespace App\Services\Bps\Models;

class ParcelDetails extends BaseModel{

    public $apply_min_dimension_override = true;
    public $destination_country_iso;
    public $weight;
    public $length;
    public $width;
    public $height;
    public $value;
    public $freight_value;
    public $insurance_value;
    public $service_id;
    public $tax_modality;
    public $measurement_unit;
    public $weight_unit;
    public $parcel_type;

}