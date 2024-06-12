<?php

namespace App\Rules;

use App\Models\Country;
use App\Models\State;
use App\Models\ZipCode;
use Illuminate\Contracts\Validation\Rule;

class ZipCodeValidator implements Rule
{
    private $country;
    
    private $state;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($countryId=null,$stateId=null)
    {
        $this->country = Country::find($countryId);

        $this->state = State::find($stateId);
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
        return true;
        if ( !$this->country ){
            return true;
        }

        if ( $this->country->code !== 'BR' ){
            return true;
        }

        if ( !$this->state ){
            return false;
        }

        if ( ZipCode::where('zipcode',$value)->where('country_id',$this->country->id)->where('state',$this->state->code)->first() ){
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.invalid_zipcode',[
            'link' => "<a href='".route('zipcode.search')."' target='_blank'> Find Zip Code/ Encontre o código postal </a>"
        ]);
    }
}
