<?php 

namespace App\Repositories\Calculator; 
use App\Services\Calculators\WeightCalculator;
use App\Models\Recipient;
use App\Models\Order;
use App\Models\ShippingService;
use App\Models\User;
use App\Services\Converters\UnitsConverter;
use Illuminate\Support\Facades\DB;
use Auth;

class CalculatorRepository {

    protected $error;
    public $order;
    public $recipient;
    public $chargableWeight;

    public function handel($request)
    {

        $originalWeight =  $request->weight;
        if ( $request->unit == 'kg/cm' && !$request->weight_discount){
            $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'cm');
            $this->chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }elseif($request->unit == 'lbs/in' && !$request->weight_discount){
            $volumetricWeight = WeightCalculator::getVolumnWeight($request->length,$request->width,$request->height,'in');
            $this->chargableWeight = round($volumetricWeight >  $originalWeight ? $volumetricWeight :  $originalWeight,2);
        }else{
            $this->chargableWeight = $request->discount_volume_weight;
        }

        if($this->createRecipient($request)){

            if($this->createOrder($request)){
                return $this->order;
            };
        }
    }

    private function createRecipient($request){
        DB::beginTransaction();

        try{

        $recipient = new Recipient();
        $recipient->state_id = $request->state_id;
        $recipient->country_id = $request->country_id;
        DB::commit();

        $recipient->refresh();
        $this->recipient = $recipient;
        return $this->recipient;

        }catch (\Exception $ex) {

        DB::rollback();
        $this->error = $ex->getMessage();
        return false;

        }
    }

    private function createOrder($request){

        DB::beginTransaction();

        try{

            $order = new Order();
            $order->id = 1;
            $order->user = Auth::user() ? Auth::user() :  User::where('role_id',1)->first();
            $order->width = $request->width;
            $order->height = $request->height;
            $order->length = $request->length;
            $order->weight = $request->weight;
            $order->measurement_unit = $request->unit;
            $order->weight_discount = $request->weight_discount;
            $order->recipient = $this->recipient;
            DB::commit();
            $order->refresh();
            $this->order = $order;

            return true;
        } catch (\Exception $ex) {

            DB::rollback();
            $this->error = $ex->getMessage();
            return false;

        }
    }


    public function getShippingService()
    {
        $message="Shipping Service not found";
        $anjunSelected = setting('anjun_api', null, User::ROLE_ADMIN) ||setting('bcn_api', null, User::ROLE_ADMIN);  
        $shippingServices = collect();
        foreach (ShippingService::query()->active()->get() as $shippingService) {
            if ($shippingService->isAvailableFor($this->order)){
                $serviceSubClass = $shippingService->service_sub_class;
                if(!$anjunSelected  && $serviceSubClass !=  ShippingService::AJ_Packet_Standard && $serviceSubClass != ShippingService::AJ_Packet_Express){
                    $shippingServices->push($shippingService);
                } 
                if($anjunSelected &&  $serviceSubClass !=  ShippingService::Packet_Standard    && $serviceSubClass != ShippingService::Packet_Express){
                    $shippingServices->push($shippingService);
                }
            }else{
                $message = "Shipping Service $shippingService->name not Available Error:{$shippingService->getCalculator($this->order)->getErrors()}";
            }
        }
        if($shippingServices->isEmpty()){
                  session()->flash('alert-danger',$message);
        }
        return $shippingServices;
    }

    public function getChargableWeight()
    {
       return $this->chargableWeight;
    }

    public function getWeightInOtherUnit($request){

        if ($request->unit == 'kg/cm' ){
            $weightInOtherUnit = UnitsConverter::kgToPound($this->chargableWeight);
        }else{
            $weightInOtherUnit = UnitsConverter::poundToKg($this->chargableWeight);
        }

        return $weightInOtherUnit;
    }

}