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
        \Log::info('invalidating');
        return __('validation.ncm.invalid');
    }
}
