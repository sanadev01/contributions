<?php

namespace App\Http\Resources\PublicApi;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderRecipientResource extends JsonResource
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
            "state_iso_code" => optional($this->state)->code,
            "country_iso_code" => optional($this->country)->code,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "phone" => $this->phone,
            "city" => $this->city,
            "street_no" => $this->street_no,
            "address" => $this->address,
            "address2" => $this->address2,
            "account_type" => $this->account_type,
            "tax_id" => $this->tax_id,
            "zipcode" => $this->zipcode,
        ];
    }
}
