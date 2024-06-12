<?php

namespace App\Http\Resources\PublicApi;

use Illuminate\Http\Resources\Json\JsonResource;

class UserShippingResource extends JsonResource
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
            'id' => $this->service_id,
            'name' => $this->shippingService->name,
            'service_sub_class' => $this->shippingService->service_sub_class,
        ];
    }
}