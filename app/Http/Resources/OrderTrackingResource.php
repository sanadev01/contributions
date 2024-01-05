<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return 
        return [
            "id"=> $this->id,
            "order_id"=> $this->order_id,
            "country"=> $this->country,
            "city"=> $this->city,
            "status_code"=> $this->status_code,
            "type"=> $this->type,
            "description"=> $this->description,
            "created_by"=> $this->created_by,
            "updated_by"=> $this->updated_by,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at, 
            'tracking_id' => $this->order->tracking_id,
            'tracking_code' => $this->order->corrios_tracking_code,
            'city' => $this->order->recipient->city,
            'state' => optional(optional(optional($this->order)->recipient)->State)->code,
            'zipcode' => $this->order->recipient->zipcode,
            'country' => $this->order->recipient->country->code,
        ];
    }
}
