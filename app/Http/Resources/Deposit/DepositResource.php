<?php 
namespace App\Http\Resources\Deposit;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Deposit\OrderResource;
use Carbon\Carbon;

class DepositResource extends JsonResource{
    public function toArray($request)
    { 
       return [ 
        'type' => $this->type,
        'id' => $this->uuid,
        'amount' => $this->amount,
        'description' => $this->description,
        'detail' => $this->last_four_digits,
        'created_at' => Carbon::parse($this->created_at)->format('Y-M-d'),
        'orders' => $this->orders?OrderResource::collection($this->orders()->withTrashed()->get()):[],
       ];
    }

}