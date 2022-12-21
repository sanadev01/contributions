<?php

namespace App\Http\Controllers\Warehouse;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use Carbon\Carbon;

class SwedenPostUnitRegisterController extends Controller
{
    public function __invoke(Container $container)
    {
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger','Please add parcels to this container');
            return back();
        }

        $date = date('YmdHis', strtotime(Carbon::now()));
        $code = "SPHD".''.$date;

        $container->update([
            'unit_code' => $code,
            'response' => '1',
        ]);

        $this->addOrderTracking($container);
        session()->flash('alert-success','Package Registration success. You can print Label now');
        return back();
    }

    public function addOrderTracking($container)
    {
        $orders = $container->orders;

        foreach ($orders as $order)
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_SHIPPED,
                'type' => 'HD',
                'description' => 'Parcel transfered to airline',
                'country' => 'US',
                'city' => 'Miami'
            ]);
        }

        return true;
    }
}
