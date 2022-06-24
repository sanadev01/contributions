<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class InventoryOrderUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|numeric|gt:0',
            'weight' => 'required|numeric|gt:0',
            'length' => 'required|numeric|gt:0',
            'width' => 'required|numeric|gt:0',
            'height' => 'required|numeric|gt:0',
            'measurement_unit' => 'required|in:kg/cm,lbs/in'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $order = Order::find($request->order_id);

        if ($order) {
            $order->update([
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'measurement_unit' => $request->measurement_unit,
                'status'  => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Order not found'
        ]);
    }
}