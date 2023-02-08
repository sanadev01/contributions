<?php 
namespace App\Http\Resources\Deposit;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource{
    public function toArray($request)
    {
       return [
            'warehouse_number' => $this->warehouse_number,
            'corrios_tracking_code' => $this->corrios_tracking_code,
       ];
    }

}