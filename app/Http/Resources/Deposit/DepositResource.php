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
            'pobox_number' => $this->user->pobox_number,
            'amount' => $this->amount,
            'description' => $this->description,
            'created_at' => Carbon::parse($this->created_at)->format('Y-M-d'),
            'orders' => $this->orders?OrderResource::collection($this->orders()->withTrashed()->get()):[],
           
       ];
    }

}