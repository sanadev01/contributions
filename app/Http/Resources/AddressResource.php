<?php

namespace App\Http\Resources;

use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'state_id' => $this->state_id,
            'country_id' => $this->country_id,
            'state_code' => State::find($this->state_id)->code,
            'country_code' => Country::find($this->country_id)->code,
            'city' => $this->city,
            'address' => $this->address,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'zip_code' => $this->zipcode,
        ];
    }
}
