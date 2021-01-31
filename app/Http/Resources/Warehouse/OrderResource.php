<?php

namespace App\Http\Resources\Warehouse;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_date' => $this->order_date,
            'merchant' => $this->merchant,
            'carrier' => $this->carrier,
            'carrier_tracking_id' => $this->carrier_tracking_id,
            'shipment' => [
                'weight' => $this->weight,
                'height' => $this->height,
                'length' => $this->length,
                'quantity' => $this->quantity,
                'unit' => $this->measurement_unit,
                'whr_number' => $this->warehouse_number
            ]
        ];
    }
}
