<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ConsolidationPrintController extends Controller
{
    public function __invoke(Order $parcel)
    {
        return view('admin.modals.parcels.consolidation-print',compact('parcel'));
    }
}
