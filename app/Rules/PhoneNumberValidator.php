<?php

namespace App\Rules;

use App\Models\Country;
use Illuminate\Contracts\Validation\Rule;

class PhoneNumberValidator implements Rule
{
    private $country;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($countryId=null)
    {
        $this->country = Country::find($countryId);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $number)
    {
        
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $numberProto = $phoneUtil->parse($number, __default( optional($this->country)->code,'BR'));
            $countryCode = $phoneUtil->getRegionCodeForNumber($numberProto);
            
            if ($countryCode != optional($this->country)->code) {
                return false;
            }

            return $phoneUtil->isValidNumber($numberProto);

        } catch (\libphonenumber\NumberParseException $e) {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.invalid_number',['country'=>__default( optional($this->country)->name,'BR')]);
    }
}
