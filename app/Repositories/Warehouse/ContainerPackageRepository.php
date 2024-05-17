<?php

namespace App\Repositories\Warehouse;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AbstractRepository;
use App\Services\Correios\Services\Brazil\Client;
use App\Http\Resources\Warehouse\Container\PackageResource;

class ContainerPackageRepository extends AbstractRepository
{

    public function store(Request $request)
    {
        try {
            return  Container::create([
                'user_id' => Auth::id(),
                'dispatch_number' => Container::getDispatchNumber(),
                'origin_country' => 'US',
                'origin_operator_name' => 'HERC',
                'postal_category_code' => 'A',
                'destination_operator_name' => $request->destination_operator_name,
                'unit_type' => $request->unit_type,
                'services_subclass_code' => $request->services_subclass_code
            ]);
            
        $order = Order::where('corrios_tracking_code', strtoupper($barcode))->first();

        if (!$this->isValidContainerOrder($container, $order)) {
            return $this->validationError404($barcode, 'Order Not Found. Please Check Packet Service.');
        }
        if ($container->has_anjun_china_service) {
            return $this->toAnjunChinaContainer($container, $barcode);
        }
        if ($container->hasBCNService()) {
            return $this->toBCNContainer($container, $barcode);
        }
        $order = Order::where('corrios_tracking_code', strtoupper($barcode))->first();

        if(!$this->isValidContainerOrder($container,$order)) {
             return $this->validationError404($barcode, 'Order Not Found. Please Check Packet Service.');
        }
        $containerOrder = $container->orders->first();
        if ($containerOrder) {
            $client = new Client();
            $newResponse = $client->getModality($barcode);
            $oldResponse = $client->getModality($containerOrder->corrios_tracking_code);
            if ($newResponse != $oldResponse) {

                return $this->validationError404($barcode, 'Order Service is changed. Please Check Packet Service');
            }
        }

        if (!$order) {
            return $this->validationError404($barcode, 'Order Not Found.');
        }

        if ($order->status < Order::STATUS_PAYMENT_DONE) {
            return $this->validationError404($barcode, 'Please check the Order Status, either the order has been canceled, refunded or not yet paid');
        }
        \Log::info([
            'container' => $container->services_subclass_code,
            'is anjun container' => $container->has_anjun_service,
            'order subclass' => $order->shippingService->service_sub_class,
            'is anjun order' => $order->shippingService->is_anjun_service
        ]);
        if (!$container->has_anjun_service || !$order->shippingService->is_anjun_service) {
            return $this->validationError404($barcode, 'Order does not belongs to this container Service. Please Check Packet Service');
        }

        return $this->updateContainer($container, $order, $barcode);
    }
    public function toAnjunChinaContainer(Container $container, string $barcode)
    {
        $order = Order::where('corrios_tracking_code', strtoupper($barcode))->first();

        if (!$order) {
            return $this->validationError404($barcode,  'Order Not Found.');
        }

        if ($order->status < Order::STATUS_PAYMENT_DONE) {
            return $this->validationError404($barcode, 'Please check the Order Status, either the order has been canceled, refunded or not yet paid');
        }
        // $subString = strtolower(substr($barcode,0,2));
        // if($subString != 'nb' && $subString != 'xl'){
        //     return $this->validationError404($barcode, 'Order does not belongs to this anjun china container Service. Please Check Packet Service');
        //  }
        if (!$order->shippingService->is_anjun_china_service) {

            return $this->validationError404($barcode, 'Order does not belongs to this container Service. Please Check Packet Service');
        }

        if ($container->has_anjun_china_standard_service && !$order->shippingService->is_anjun_china_standard_service) {

            return $this->validationError404($barcode, 'Order does not belongs to this standard container Service. Please Check Packet Service');
        }
        if ($container->has_anjun_china_express_service && !$order->shippingService->is_anjun_china_express_service) {

            return $this->validationError404($barcode, 'Order does not belongs to this container express Service. Please Check Packet Service');
        }

        return $this->updateContainer($container, $order, $barcode);
    }

    public function toBCNContainer(Container $container, string $barcode)
    {
        $order = Order::where('corrios_tracking_code', strtoupper($barcode))->first();

        if (!$order) {
            return $this->validationError404($barcode,  'Order Not Found.');
        }

        if ($order->status < Order::STATUS_PAYMENT_DONE) {
            return $this->validationError404($barcode, 'Please check the Order Status, either the order has been canceled, refunded or not yet paid');
        }
        if (!$order->shippingService->is_bcn_service) {

            return $this->validationError404($barcode, 'Order does not belongs to this container Service. Please Check Packet Service');
        }

        if ($container->has_bcn_express_service && !$order->shippingService->is_bcn_express) {

            return $this->validationError404($barcode, 'Order does not belongs to this standard container Service. Please Check Packet Service');
        }
        if ($container->has_bcn_standard_service && !$order->shippingService->is_bcn_standard) {

            return $this->validationError404($barcode, 'Order does not belongs to this container express Service. Please Check Packet Service');
        }

        return $this->updateContainer($container, $order, $barcode);
    }
    public function updateContainer($container, $order, $barcode)
    {
        $containerOrder = $container->orders->first();
        $firstOrderGroupRange = getOrderGroupRange($containerOrder);

        if ($containerOrder) {
            
            if (optional($containerOrder->order_date)->greaterThanOrEqualTo(Carbon::parse('2024-01-22'))) {

                // if ($order->order_date && $order->order_date < $containerOrder->order_date) {
                //     $firstOrderDate = optional($containerOrder->order_date)->format('Y-m-d');
                //     return $this->validationError404($barcode, 'Order date should be greater than or equal to the first container order date (' . $firstOrderDate . ')');
                // }

                // If the first order's zipcode is not in the specified group ranges, return an error
                if ($firstOrderGroupRange === null) {
                    return $this->validationError404($barcode, 'Invalid zipcode range for container');
                }

                // Check if the current order's zipcode falls within the same group range
                $currentOrderGroupRange = getOrderGroupRange($order);
                if ($currentOrderGroupRange['group'] !== $firstOrderGroupRange['group']) {
                    $currentOrderZipcode = $order->recipient->zipcode;
                    $validRangeGroup = "Group {$firstOrderGroupRange['group']}";
                    $validRangeStart = $firstOrderGroupRange['start'];
                    $validRangeEnd = $firstOrderGroupRange['end'];

                    $validRange = "Valid range: $validRangeGroup (Start: $validRangeStart, End: $validRangeEnd)";
                    
                    return $this->validationError404($barcode, "Invalid Zipcode Group for container. Valid Group is {$firstOrderGroupRange['group']}");
                }
            } else {
                
                if ($containerOrder->getOriginalWeight('kg') <= 3 && $order->getOriginalWeight('kg') > 3) {

                    return $this->validationError404($barcode, 'Order weight is greater then 3 Kg, Please Check Order Weight');
                } elseif ($containerOrder->getOriginalWeight('kg') > 3 && $order->getOriginalWeight('kg') <= 3) {
    
                    return $this->validationError404($barcode, 'Order weight is less then 3 Kg, Please Check Order Weight');
                }
            }
        }        

        if (!$order->containers->isEmpty()) {
            return $this->validationError404($barcode, 'Order Already in Container.');
        }

        $container->orders()->attach($order->id);

        $this->addOrderTracking($order->id);

        $order->error = null;
        $order->code = 200;

        return [
            'order' => (new PackageResource($order))
        ];
    }

    public function removeOrderFromContainer(Container $container, Order $order)
    {
        $container->orders()->detach($order->id);

        return $this->removeOrderTracking($order->id);
    }

    public function addOrderTracking($id)
    {
        OrderTracking::create([
            'order_id' => $id,
            'status_code' => Order::STATUS_INSIDE_CONTAINER,
            'type' => 'HD',
            'description' => 'Parcel inside Homedelivery Container',
            'country' => 'US',
            'city' => 'Miami'
        ]);

        return true;
    }

    public function removeOrderTracking($id)
    {

        $order_tracking = OrderTracking::where('order_id', $id)->latest()->first();

        return $order_tracking->delete();
    }
    public function isValidContainerOrder($container, $order)
    {
        if (!$order)
            return false;
        $barcode = $order->corrios_tracking_code;
        $subString = strtolower(substr($barcode, 0, 2));
        if (strtolower(substr($barcode, 0, 2)) == 'na' || strtolower(substr($barcode, 0, 2)) == 'xl' || strtolower(substr($barcode, 0, 2)) == 'nc' || strtolower(substr($barcode, 0, 2)) == 'nb') {
            $subString = 'nx';
        }
        return strtolower($container->subclass_code)  == $subString;
    }
    public function validationError404($barcode, $message)
    {
        return [
            'order' => [
                'corrios_tracking_code' => $barcode,
                'error' => $message,
                'code' => 404
            ],
        ];
    }
}
