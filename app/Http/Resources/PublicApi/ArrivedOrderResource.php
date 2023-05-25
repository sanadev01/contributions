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
            
            "corrios_tracking_code"=> $this->corrios_tracking_code ,
            "pobox_number"=> $this->user->pobox_number ,
            "driver_tracking_name"=> optional(optional($this->driverTracking)->user)->name ,
            "merchant"=> $this->merchant ,
            "Dimensions"=> $this->length ."X".$this->length ."X". $this->height,
            "kg_weight"=> number_format($this->getWeight('kg'),2),
            "id"=> $this->id ,
            "tracking_id"=> $this->tracking_id ,
            "recipient_first_name"=> $this->recipient->first_name ,
            "order_date"=> $this->order_date ,
            "driver_tracking_created_at"=> optional(optional($this->driverTracking)->created_at)->format('m-d-y') ,
            "arrived_date"=> $this->arrived_date ,
            'status'=>$this->status < 80?"Scanned in the warehouse":'Shipped'  , 
        ];
    }
 
}
