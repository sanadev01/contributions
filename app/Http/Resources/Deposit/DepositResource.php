<?php 
namespace App\Http\Resources\Deposit;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Order;
use Carbon\Carbon;

class DepositResource extends JsonResource{
    public function toArray($request)
    { 
        $order = count($this->orders)?$this->orders()->withTrashed()->first():Order::where('id',$this->order_id)->first();
       return [ 
        'type' => $this->type,
        'id' => $this->uuid,
        'warehouse_number' => optional($order)->warehouse_number,
        'amount' => $this->amount,
        'description' => $this->description,
        'detail' => $this->last_four_digits,
        'created_at' => Carbon::parse($this->created_at)->format('Y-M-d'), 
       ];
    }

}