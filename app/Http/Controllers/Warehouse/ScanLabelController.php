<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Warehouse\ScanLabelRepository;

class ScanLabelController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('driver') || auth()->user()->isAdmin()) {
            return view('admin.scan-label.index');
        }

        abort(403, 'Unauthorized action.');
    }

    public function store(Request $request, ScanLabelRepository $scanLabelRepository)
    {
        sleep(1);
        $order = Order::where('corrios_tracking_code', $request->tracking_code)->first();
        

        if (!$order) {
           
            return response()->json([
                'success' => false,
               'message' => 'sorry! parcel not found'
            ], 200);
        }

        $scanLabelRepository->handle($order);

        return response()->json([
            'success' => $scanLabelRepository->getStatus(),
            'message' => $scanLabelRepository->getMessage(),
         ], 200);
    }

    public function create()
    {
        return view('admin.scan-label.show');
    }
}
