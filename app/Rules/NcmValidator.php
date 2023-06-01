<?php

namespace App\Rules;

use App\Models\ShCode;
use App\Models\ShippingService;
use Illuminate\Contracts\Validation\Rule;

class NcmValidator implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $service;

    public function __construct($service)
    {
        $this->service = ShippingService::find($service);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if( $this->service->isGDEService() && strlen($value) != 10 ) {
            return false;
        }
        if( !$this->service->isGDEService() && strlen($value) !=6 ){
            return false;
        }
        
        $found = ShCode::where('code',$value)->first();
        return $found;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.ncm.invalid')." (:input)";
    }
}
