<?php

namespace App\Http\Controllers\Admin\Modals;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Auth;

class OrderInvoiceModalController extends Controller
{
    public function __invoke(Order $order)
    {
        $services = $order->services;

        if ($order->services->filter(function ($service) {
            return $service->name == 'Insurance';
        }) &&  $order->user->insurance == false) {
            $services = $this->calculateInsurance($order);
        }

        $volumeWeight = ($order->is_weight_in_kg) ? $order->getWeight('kg') : $order->getWeight('lbs');

        if($order->shippingService->is_total_express) {
            $appliedVolumeWeight = $volumeWeight;
        } else {
            $appliedVolumeWeight = ($order->weight_discount) ? $this->calculateDiscountedWeight($order->getOriginalWeight(), $volumeWeight, $order->weight_discount) : null;
        }
        
        if(!Auth::user()->isActive()){
            return redirect()->route('admin.modals.user.suspended');
        }
        return view('admin.modals.orders.invoice', compact('order', 'services', 'appliedVolumeWeight'));
    }

    private function calculateInsurance($order)
    {
        foreach ($order->services as $service) {
            if ($service->name == 'Insurance' || $service->name == 'Seguro') {

                $total_insurance = (3 / 100) * $order->order_value;

                if ($total_insurance > 35) {
                    $service->price = $total_insurance;
                }
            }
        }

        return $order->services;
    }

    private function calculateDiscountedWeight($originalWeight, $volumeWeight, $discountWeight)
    {
        $consideredWeight = $volumeWeight - $originalWeight;
        return ($consideredWeight - $discountWeight) + $originalWeight;
    }
}
