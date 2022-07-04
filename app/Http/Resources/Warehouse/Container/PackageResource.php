<?php

namespace App\Http\Resources\Warehouse\Container;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'weight_kg' => $this->getWeight('kg'),
            'weight_lbs' => $this->getWeight('lbs'),
            'weight' => $this->getOriginalWeight('kg'),
            'corrios_tracking_code' => $this->corrios_tracking_code,
            'warehouse_number' => $this->warehouse_number,
            'sender_name' => $this->getSenderFullName(),
            'pobox' => optional($this->user)->pobox_number.' / '.optional($this->user)->getFullName(),
            'customer_reference' => $this->customer_reference,
            'code' => $this->code,
            'error' => $this->error,
        ];
    }
}
