<?php

namespace App\Repositories\Warehouse;

use Illuminate\Http\Request;
use App\Facades\MileExpressFacade;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use App\Repositories\AbstractRepository;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use App\Services\Correios\Models\PackageError;

class ContainerRepository extends AbstractRepository{

    protected $addAirwayBill;

    public function __construct($addAirwayBill=null)
    {
        $this->client = new GuzzleClient([]);

        if (app()->isProduction()) {
            $addAirwayBill = config('postnl.production.addAirwayBill');
        }else {
            $addAirwayBill = config('postnl.testing.addAirwayBill');
        }
        $this->addAirwayBill = $addAirwayBill;
    }

    private function getKeys()
    {
        $headers = [
            'api_key' => "Eo3qtkGlOh6t9S1HZxMvFkBSJYDTocatwMhBNwhnEoG7Jngng89GtVFmQOrc05OzcMwyLMTeQSYU2h4GsOOp0iy9Rp0qoYlhpGLfLpjNc8CuV3xqbrTGFYNkiZW6TWzdJWVgEsVLg64hYMLY1UElGjrOvxBpA4aI5prbWIefoMrd85y5WkuL1RQrfkH9vRCwod0v8feftgdEeZLYUkQWfYa1TVeeEe4fcbdk9twD6ynpjmq4E7FSLwdeiFIhqicw7a1kY63Bksp5ECq1pefkn0ROrCNjpy3TPdeLKO5I6LBc",
            'Accept' => "application/json",
            'Content-Type' => "application/json",
        ];
        return $headers;
    }

    public function get(Request $request)
    {
        
        $query = Container::query();

        if ( !Auth::user()->isAdmin() ){
            $query->where('user_id',Auth::id());
        }
        if($request->has('search')){
            $query->where('dispatch_number', 'LIKE', '%' . $request->search . '%')
            ->orWhere('seal_no', 'LIKE', '%' . $request->search . '%');
        }
        if($request->filled('dispatchNumber')){
           $query->where('dispatch_number', 'LIKE', '%' . $request->dispatchNumber . '%');
        } 
        if($request->filled('sealNo')){
          $query->where('seal_no', 'LIKE', '%' . $request->sealNo . '%');
        }
        if($request->filled('packetType')){
            $packetType = [$request->packetType];
        }else{
           $packetType= ['NX','IX', 'XP','AJ-NX','AJ-IX','AJC-NX','AJC-IX']; 
        }

        if($request->filled('unitCode')){
            $query->where('unit_code', 'LIKE', '%' . $request->unitCode . '%');
        }
        
        if ($request->has('typeMileExpress')) {
            return $query->whereIn('services_subclass_code', ['ML-EX'])->latest()->paginate(50);
        }
        
        if ($request->has('typeColombia')) {
            return $query->whereIn('services_subclass_code', ['CO-NX'])->latest()->paginate(50);
        }

        return $query->whereIn('services_subclass_code', $packetType)->latest()->paginate(50);
    }

    public function store(Request $request)
    {
        try { 
            if (in_array($request->services_subclass_code, [Container::CONTAINER_ANJUN_NX, Container::CONTAINER_ANJUN_IX,Container::CONTAINER_ANJUNC_NX,Container::CONTAINER_ANJUNC_IX]) ) {
                
                $latestAnujnContainer = Container::where('services_subclass_code', Container::CONTAINER_ANJUN_NX)
                                                    ->orWhere('services_subclass_code', Container::CONTAINER_ANJUN_IX)
                                                    ->orWhere('services_subclass_code', Container::CONTAINER_ANJUNC_NX)
                                                    ->orWhere('services_subclass_code', Container::CONTAINER_ANJUNC_IX)
                                                    ->latest()->first();

                $anjunDispatchNumber = ($latestAnujnContainer->dispatch_number ) ? $latestAnujnContainer->dispatch_number + 1 : 295000;
            }

            if ($request->services_subclass_code == Container::CONTAINER_MILE_EXPRESS) {
                $response = MileExpressFacade::createContainer($request);

                if ($response->success == false) {
                    $this->error = $response->error;
                    return false;
                }

                $mileExpressContinerData = $response->data['data'];
            }

            $container =  Container::create([
                'user_id' => Auth::id(),
                'dispatch_number' => 0,
                'origin_country' => 'US',
                'seal_no' => $request->seal_no,
                'origin_operator_name' => 'HERC',
                'postal_category_code' => 'A',
                'destination_operator_name' => $request->destination_operator_name,
                'origin_airport' => $request->origin_airport,
                'flight_number' => $request->flight_number,
                'unit_type' => $request->unit_type,
                'services_subclass_code' => $request->services_subclass_code,
                'unit_response_list' => ($request->services_subclass_code == Container::CONTAINER_MILE_EXPRESS) ? json_encode($mileExpressContinerData) : null,
            ]);
            $container->update([
                'dispatch_number' => ($container->hasAnjunService()) ? $anjunDispatchNumber : $container->id,
            ]);

            return $container;

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function update(Container $container, Request $request)
    {
        try {
            return  $container->update([
                'destination_operator_name' => $request->destination_operator_name,
                'seal_no' => $request->seal_no,
                'unit_type' => $request->unit_type,
                'origin_airport' => $request->origin_airport,
                'flight_number' => $request->flight_number,
            ]);

        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function delete(Container $container, bool $force = false)
    {
        try {
            if ( $force ){
                $container->forceDelete();
            }

            $container->deliveryBills()->delete();
            $container->orders()->sync([]);
            $container->delete();

            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return null;
        }
    }

    public function addOrderToContainer($container, $orderId)
    {
        try {
            $container->orders()->attach($orderId);
            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function removeOrderFromContainer($container, $id)
    {
        try {
            $container->orders()->detach($id);
            return true;
        } catch (\Exception $ex) {
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function getAirWayBillIdsForMileExpress($containerOrders)
    {
        $airWayBillIds = [];
        
        foreach ($containerOrders as $order) {
            $orderApiResponse = json_decode($order->api_response);
            
            array_push($airWayBillIds, $orderApiResponse->data->volumes[0]->air_waybill_id);
        }

        return $airWayBillIds;
    }
    
    public function updateawb($request)
    {
        if(json_decode($request->data)){
            foreach(json_decode($request->data) as $containerId){
                $container = Container::find($containerId);

                if($container->services_subclass_code == 'PostNL' && !is_null($container->deliveryBills[0]->cnd38_code)) {
                    try {
                        $response = $this->client->post($this->addAirwayBill,[
                            'headers' => $this->getKeys(),
                            'json' => [
                                "delivery" => $container->deliveryBills[0]->cnd38_code,
                                "hawb" => '',
                                "mawb" => $request->awb,
                            ]
                        ]);
                        $data = json_decode($response->getBody()->getContents());
                        if($data->status == 'success'){
                            $container->awb  = $request->awb;
                        } else {
                            return session()->flash('alert-danger', $data->message->payload);
                        }
                    }catch (ClientException $e) {
                        return new PackageError($e->getResponse()->getBody()->getContents());
                    }
                } else {
                    $container->awb  = $request->awb;
                }
                $container->save();
                return session()->flash('alert-success', 'Airway Bill Assigned');

            }
        }
    }


}
