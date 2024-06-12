<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ShipmentModalController extends Controller
{
    public function __invoke(Order $parcel)
    {
        return view('admin.modals.parcels.shipment-info',compact('parcel'));
    }
}
