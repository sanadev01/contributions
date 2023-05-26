<?php

namespace App\Http\Resources\PublicApi;

use Illuminate\Http\Resources\Json\JsonResource;

class ArrivedOrderResource extends JsonResource
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

            "tracking_code" => $this->corrios_tracking_code,
            "merchant" => $this->merchant,
            "dimensions" => $this->length . " X " . $this->width . " X " . $this->height,
            "weight" => number_format($this->getWeight('kg'), 2),
            "recipient" => $this->recipient->first_name,
            "order_date" => $this->order_date->format('Y-m-d'),
            "picked_at" => optional(optional($this->driverTracking)->created_at)->format('Y-m-d'),
            "arrived_date" => $this->arrived_date,
            "status" => $this->status < 80 ? "Scanned in the warehouse" : 'Shipped',
        ];
    }
}
