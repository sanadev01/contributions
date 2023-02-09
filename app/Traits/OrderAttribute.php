<?php 

namespace App\Traits;

trait OrderAttribute{

    public function getOrderWarehouseNoAttribute()
    {
        $warehouseNo = '';
        if($this->order_id) {
            $warehouseNo = $this->order->warehouse_number;
        } else {
            $order = ($this->orders) ? $this->orders->first() : null;

            $warehouseNo = $this->order_id.':'."Order Deleted";
        }
        return $warehouseNo;
    }

    public function getOrderCustomerReferenceAttribute()
    {
        $customerReference = '';
        if($this->order_id) {
            $customerReference = $this->order->customer_reference;
        } else {
            $order = ($this->orders) ? $this->orders->first() : null;

            $customerReference = optional($order)->customer_reference;
        }

        return $customerReference;
    }
 
    public function getOrderRecipientNameAttribute()
    {
        $recepientName = '';
        if($this->order_id) {
            $recepientName = optional($this->order->recipient)->fullName();
        } else {
            $order = ($this->orders) ? $this->orders->first() : null;
            $recepientName = optional(optional($order)->recipient)->fullName();
        }
        return $recepientName;
    } 

    public function getOrderDimensionsAttribute()
    {
        $dimensions = '';
        if($this->order_id) {
            $dimensions = $this->order->length.'x'.$this->order->width.'x'.$this->order->height;
        } else {
            $order = ($this->orders) ? $this->orders->first() : null;
            if($order) 
                $dimensions = $order->length.'x'.$order->width.'x'.$order->height;
            
        }
        return $dimensions;
    } 

    public function getOrderWeightAttribute()
    {
        $weight = '';
        if($this->order_id) {
            $weight = $this->order->weight;
        } else {
            $order = ($this->orders) ? $this->orders->first() : null;
            if($order)
            $weight = $order->weight;
        }
        return $weight;
    }

    public function getOrderTrackingCodeAttribute() 
    {
        $trackingCode = '';
        if($this->hasOrder() && $this->firstOrder()->hasSecondLabel()) {
            $trackingCode = $this->firstOrder()->us_api_tracking_code;
        }elseif($this->order_id) {
            $trackingCode = $this->order->corrios_tracking_code;
        }
        return $trackingCode;
    }

}