<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\DuplicateOrderRepository;

class DuplicatePreAlertController extends Controller
{
    public function __invoke(Order $order, DuplicateOrderRepository $duplicateOrderRepository)
    {
        $this->authorize('duplicatePreAlert',$order);
        
        $parcel = $duplicateOrderRepository->makeDuplicatePreAlert($order);

        session()->flash('alert-success','Parcels Duplicate');
        return redirect()->route('admin.parcels.edit',$parcel->encrypted_id);
    }
}
