<?php 
namespace App\Http\Resources\Deposit;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Deposit\OrderResource;
use Carbon\Carbon;

class DepositResource extends JsonResource{
    public function toArray($request)
    { 
       return [
            'orders' => $this->orders?OrderResource::collection($this->orders()->withTrashed()->get()):[],
            'pobox_number' => $this->user->pobox_number,
            'amount' => $this->amount,
            'type' => $this->type,
            'description' => $this->description,
            'created_at' => Carbon::parse($this->created_at)->format('Y-M-d'),
       ];
    }

}