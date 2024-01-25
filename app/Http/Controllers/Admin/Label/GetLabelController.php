<?php

namespace App\Http\Controllers\Admin\Label;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Exception;

class GetLabelController extends Controller
{
    public function __invoke($id)
    {
  
        try{
            $order = Order::find(decrypt($id));
            \Log::info('order detail',[$order]);
            if($order->corrios_tracking_code){  
                if ( $order->sinerlog_url_label != '' ) {
                    return redirect($order->sinerlog_url_label);
                } else {
                    if ( !file_exists(storage_path("app/labels/{$order->corrios_tracking_code}.pdf")) ){
                        return apiResponse(false,"Lable Expired or not generated yet please update lable");
                    }
                }
                return response()->download(
                    storage_path("app/labels/{$order->corrios_tracking_code}.pdf"),
                    "{$order->corrios_tracking_code} - {$order->warehouse_number}.pdf",
                    [
                        'Content-Disposition' => 'attachment; filename="' . "{$order->corrios_tracking_code} - {$order->warehouse_number}.pdf" . '"'
                    ],
                    'inline'
                );
              }
            return apiResponse(false,'Something Went wrong please Contact Homedelivery support');

        }catch(Exception $e){
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
            422);
        }
 }
}
