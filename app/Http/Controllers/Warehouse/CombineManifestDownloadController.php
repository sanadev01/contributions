<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Validator;
use App\Services\Excel\Export\ExportCombineManfestService;

class CombineManifestDownloadController extends Controller
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
            'dbills' => 'required|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $deliveryBillIds = explode(',', $request->dbills[0]);
        $deliveryBills = DeliveryBill::whereIn('id', $deliveryBillIds)->get();
        
        if ($deliveryBills->count() > 0) {
            
            $exportService = new ExportCombineManfestService($deliveryBills);
            return $exportService->handle();
        }

        return back()->withErrors(['dbills' => 'No delivery bills found.']);
    }
}