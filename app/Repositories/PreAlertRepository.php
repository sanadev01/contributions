<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreAlertRepository extends Model
{
    public function store(Request $request)
    {
        $data = [];

        $request->merge([
            'status' => Order::STATUS_PREALERT_TRANSIT
        ]);

        $data = [ 'merchant', 'carrier', 'tracking_id', 'order_date', 'user_id','status'];

        if ( Auth::user()->can('addWarehouseNumber',Order::class) ){
            $request->merge([
                'warehouse_number' => $request->whr_number
            ]);
            $data[] = 'warehouse_number';
        }

        if ( Auth::user()->can('addShipmentDetails',Order::class) ){
            $request->merge([
                'measurement_unit' => $request->unit,
                'is_shipment_added' => true
            ]);

            $data[] = 'weight';
            $data[] = 'measurement_unit';
            $data[] = 'length';
            $data[] = 'width';
            $data[] = 'height';
            
            $request->merge([
                'status' => Order::STATUS_PREALERT_READY
            ]);
        }

        if ( !Auth::user()->isAdmin() ){
            $request->merge([
                'user_id' => Auth::id()
            ]);
        }

        $order = Order::create(
            $request->only($data)
        );

        if ( Auth::user()->can('addShipmentDetails',Order::class) ){
            $order->update([
                'warehouse_number' => "TEMPWHR-{$order->id}"
            ]);
        }

        return $order;

        // if($this->hasFile('invoiceFile')){
        //     $data['invoiceFile'] = 'required|file';
        // }
    }
}
