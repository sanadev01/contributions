<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\DeliveryBill;
use App\Http\Controllers\Controller;
use App\Services\PostPlus\CN38LabelHandler;
use Illuminate\Support\Facades\Response;

class PostPlusCN38DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(DeliveryBill $deliveryBill)
    {
        return CN38LabelHandler::handle($deliveryBill);
    }

}
