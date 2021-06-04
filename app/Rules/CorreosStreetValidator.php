<?php

namespace App\Rules;

use FlyingLuscas\Correios\Client;
use Illuminate\Contracts\Validation\Rule;

class CorreosStreetValidator implements Rule
{
    private $street_no;
    private $correos_api_street;
    private $country_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($country_id=null, $street_no = null)
    {
        $this->street_no = $street_no;
        $this->country_id = $country_id;
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
        // checking street no using zip code as according to corroes api
        if($this->country_id == '30')
        {
            $correios = new Client;
            $response = $correios->zipcode()->find($value);
            // dd($response);
            $this->correos_api_street = optional($response)['street'];
            
            if($this->street_no != $this->correos_api_street)
            {
                return false;
            } else {
                return true;
            }
        } 
        
        return true;
       
        
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->correos_api_street != null)
        {
            return 'Correos says according to your ZIP code your House Number is: "<strong>'.$this->correos_api_street. '"</strong>';
        }

        return 'Your Zip Code is Invalid, please check your Zip Code';
        
    }
}
