<?php
namespace App\Repositories\Warehouse;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Repositories\AbstractRepository;
use App\Http\Resources\Warehouse\Container\PackageResource;
use App\Models\ShippingService;
use App\Services\Correios\GetZipcodeGroup;
use App\Traits\ContainerOrderValidation;

class AddContainerPackageRepository extends AbstractRepository{
    use ContainerOrderValidation;
    private $container;
    private $barcode;
    private $order;
    private $shippingService; 
    private $containerFirstOrder;

    function __construct(Container $container, string $barcode){
        $this->container = $container;
        $this->barcode = strtoupper($barcode); 
        $this->order = Order::where('corrios_tracking_code',$barcode )->first();
        $this->shippingService =  optional($this->order)->shippingService;
        $this->containerFirstOrder = $this->container->orders->first();   
    } 
    public function addOrderToContainer()
    { 
        $startTime = microtime(true);   
        if (!$this->order) {
            return $this->validationError404('Order Not Found.');
        }
        if(!$this->orderValidate()){
            return $this->orderValidateMessage($this->container,$this->shippingService->service_sub_class);
        }
        if (!$this->order->containers->isEmpty()) {
            return $this->validationError404('Order Already in Container.');
        }
        if ($this->order->status < Order::STATUS_PAYMENT_DONE) {
            return $this->validationError404('Please check the Order Status, either the order has been canceled, refunded or not yet paid');
        }
        if(!$this->isValidContainerOrder()) {
            return $this->validationError404('Order Not Found. Please Check Packet Service.');
        }
        if (!$this->container->has_bcn_service && !$this->container->has_anjun_china_service) { 
            if (!$this->container->has_anjun_service || !$this->shippingService->is_anjun_service) {
                return $this->validationError404('Order does not belongs to this container Service. Please Check Packet Service');
            }
        }
        $outputChina= $this->updateContainer();
        $endTimeChina = microtime(true); 
        $executionTimeChina = $endTimeChina - $startTime;  
        \Log::info('Execution time of uptoUpdateTime:' . $executionTimeChina . ' seconds');
        return $outputChina;
    }
    
  
    
    public function updateContainer()
    {
        $startTime = microtime(true);   
        $output = $this->updateContainerExecutionTime( );
        $endTime = microtime(true); 
        $executionTime = $endTime - $startTime;  
        \Log::info('Execution time of updateContainer:' . $executionTime . ' seconds');
       return $output;
    }
 
    public function updateContainerExecutionTime()
    {
        if ($this->containerFirstOrder) {
                //make sure container all order are belong to same gorup.
                $firstGroup  = (new GetZipcodeGroup($this->containerFirstOrder->recipient->zipcode))->getZipcodeGroup();
                $currentGroup =  (new GetZipcodeGroup($this->order->recipient->zipcode))->getZipcodeGroup();
                if ($currentGroup !== $firstGroup){
                    return $this->validationError404("Invalid Zipcode Group for container. Valid Group is {$firstGroup}");
                }
        }else{
            //make sure tha container first order have valid group.
            $currentGroup =  (new GetZipcodeGroup($this->order->recipient->zipcode))->getZipcodeGroup(); 
            if ($currentGroup === null) {
                return $this->validationError404('Invalid zipcode range for container');
            }
        }
        $this->container->orders()->attach($this->order->id);
        $this->addOrderTracking();
        $this->order->error = null;
        $this->order->code = 200;
        return [
            'order' => (new PackageResource($this->order))
        ];
    } 

    public function addOrderTracking()
    {
        OrderTracking::create([
            'order_id' => $this->order->id,
            'status_code' => Order::STATUS_INSIDE_CONTAINER,
            'type' => 'HD',
            'description' => 'Parcel inside Homedelivery Container',
            'country' => 'US',
            'city' => 'Miami'
        ]); 
        return true;
    }
    public function validationError404($message)
    {
        return [
            'order' => [
                'corrios_tracking_code' => $this->barcode,
                'error' => $message,
                'code' => 404
            ],
        ];
    } 

}
