<?php

namespace App\Http\Controllers\Api\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Resources\Warehouse\OrderResource;
use App\Models\ApiLog;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PackageConfirmationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        ApiLog::create([
            'user_id' => Auth::id(),
            'type' => ApiLog::TYPE_CONFIRMATION,
            'data' => $request->all()
        ]);

        if ( !$request->isJson() ){
            return response()->json([
                'success' => false,
                'message' => "Bad Request Invalid Content Type. Required application/json",
                'data' => null
            ],422); 
        }

        $rules = [
            "merchant" => "required|max:191",
            "carrier" => "required|max:191",
            "carrier_tracking_id" => "required|max:191",
            "pobox_number" => "required|size:10|exists:users,pobox_number",
            'shipment' => 'required',
            'shipment.width' => 'required|numeric',
            'shipment.height' => 'required|numeric',
            'shipment.length' => 'required|numeric',
            'shipment.weight' => 'required|numeric',
            'shipment.unit' => 'required|in:lbs/in,kg/cm',
            'shipment.whr_number' => 'required|max:20',
            'shipment.images' => 'required|array|min:1|max:5',
            'shipment.images.*' => 'required|integer|exists:documents,id'
        ];

        $v = Validator::make($request->all(),$rules,[
            'shipment.width' => 'Width is required and it should be numeric',
            'shipment.height' => 'height is required and it should be numeric',
            'shipment.length' => 'length is required and it should be numeric',
            'shipment.weight' => 'weight is required and it should be numeric',
            'shipment.unit' => 'Unit is required and it must be one of lbs/in,kg/cm',
            'shipment.whr_number' => 'warehouse number is required',
            'shipment.images' => 'Images Should be array of ids',
            'shipment.images.exists' => 'This image id is not Valid. please upload image and provide valid id',
        ]);

        if ( $v->fails() ){
            return response()->json([
                'success' => false,
                'message' => "Validation Errors",
                'data' => [
                    'errors' => $v->errors()->toArray()
                ]
            ],422); 
        }

        $user = User::where('pobox_number',$request->pobox_number)->first();
        $parcel = Order::where('user_id',$user->id)
                            ->where(function($query) use($request){
                                $query->where('tracking_id',$request->carrier_tracking_id);
                                $query->orWhere('warehouse_number',$request->shipment['whr_number']);
                            })
                            ->first();

        if ( !$parcel ){
            $parcel = Order::create([
                'user_id' => $user->id,
                'order_date' => Carbon::now(),
                'merchant' => $request->merchant,
                'carrier' => $request->carrier,
                'tracking_id' => $request->carrier_tracking_id,
                'width' => $request->shipment['width'], 
                'height' => $request->shipment['height'], 
                'length' => $request->shipment['length'], 
                'weight' => $request->shipment['weight'], 
                'warehouse_number' => $request->shipment['whr_number'], 
                'measurement_unit' => $request->shipment['unit'],
                'is_received_from_sender' => true,
                'status' => Order::STATUS_PREALERT_READY,
                'is_shipment_added' => true
            ]);
        }else{
            $parcel->update([
                'is_received_from_sender' => true,
                'width' => $request->shipment['width'], 
                'height' => $request->shipment['height'], 
                'length' => $request->shipment['length'], 
                'weight' => $request->shipment['weight']
            ]);
        }

        $parcel->images()->sync(
            $request->shipment['images']
        );

        return response()->json([
            'success' => true,
            'message' => "Shipment Created/Updated",
            'data' => [
                OrderResource::make($parcel)
            ]
        ]);
    }
}
