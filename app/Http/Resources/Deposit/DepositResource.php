<?php

namespace App\Http\Resources\Deposit;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Deposit\OrderResource;
use App\Models\Order;
use Carbon\Carbon;

class DepositResource extends JsonResource
{
    public function toArray($request)
    {
        $trackingCode = null;
        $warehouseNumber = null;
        if ($this->hasOrder() && $this->firstOrder()->hasSecondLabel()) {
            $order =   $this->firstOrder();
            $trackingCode = $order->us_api_tracking_code;
            $warehouseNumber =  $order->warehouse_number;
        } elseif ($this->order_id && $this->getOrder($this->order_id)) {
            $order =   $this->getOrder($this->order_id);
            $trackingCode = $order->corrios_tracking_code;
            $warehouseNumber =  $order->warehouse_number;
        }

        return [
            'type' => $this->type,
            'id' => $this->uuid,
            'tracking_code' => $trackingCode,
            'warehouse_number' => $warehouseNumber,
            'amount' => $this->amount,
            'description' => $this->description,
            'detail' => $this->last_four_digits,
            'created_at' => Carbon::parse($this->created_at)->format('Y-M-d'),
        ];
    }
}
