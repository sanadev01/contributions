<?php

namespace App\Http\Resources\PublicApi;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            "sh_code" => $this->sh_code,
            "description" => $this->description,
            "quantity" => $this->quantity,
            "value" => $this->value,
            "is_battery" => $this->contains_battery,
            "is_perfume" => $this->contains_perfume,
        ];
    }
}
