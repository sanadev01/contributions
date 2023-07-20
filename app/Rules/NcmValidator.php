<?php

namespace App\Rules;

use App\Models\ShCode;
use Illuminate\Contracts\Validation\Rule;

class NcmValidator implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        if ( strlen($value) >=6 ){
            return true;
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
