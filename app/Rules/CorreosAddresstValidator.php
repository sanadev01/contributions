<?php

namespace App\Rules;

use FlyingLuscas\Correios\Client;
use Illuminate\Contracts\Validation\Rule;

class CorreosAddresstValidator implements Rule
{
    private $address;
    private $correos_api_street_address;
    private $country_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($country_id=null, $address = null)
    {
        $this->address = $address;
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
             
             $this->correos_api_street_address = optional($response)['street'];
             
             if(strtolower($this->address) != strtolower($this->correos_api_street_address))
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
        if($this->correos_api_street_address != null)
        {
            return 'Correos says according to your given ZIP code, your Address must be: "<strong>'.$this->correos_api_street_address. '"</strong>';
        }

        return 'Your Zip Code is Invalid, please check your Zip Code';
    }
}
