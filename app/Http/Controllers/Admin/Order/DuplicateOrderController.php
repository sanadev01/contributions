<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\DuplicateOrderRepository;
use Illuminate\Http\Request;

class DuplicateOrderController extends Controller
{
    public function __invoke(Order $order, DuplicateOrderRepository $duplicateOrderRepository)
    {
        $this->authorize('duplicateOrder',$order);
        
        $copy = $duplicateOrderRepository->makeDuplicate($order);

        session()->flash('alert-success','Order Copied');
        return redirect()->route('admin.orders.sender.index',$copy->encrypted_id);
    }
}
