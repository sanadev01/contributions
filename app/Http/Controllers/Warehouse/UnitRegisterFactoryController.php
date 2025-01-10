<?php

namespace App\Http\Controllers\Warehouse;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Cainiao\Client;
use App\Services\TotalExpress\Services\TotalExpressMasterBox;
class UnitRegisterFactoryController extends Controller
{
    public $containerId;
    public function createMasterBox(Request $request)
    {
        $container = Container::find($request->id);
        $this->containerId = date('d') . date('m') . sprintf("%07d", $container->id);
        if ($container->orders->isEmpty()) {
            session()->flash('alert-danger', 'Please add parcels to this container');
            return back();
        }
        if ($container->unit_code) {
            session()->flash('alert-danger', 'Unit is already registerd; unit code :-' . $container->unit_code);
            return back();
        }

        $serviceSubClass = $container->getSubClassCode();
        if (in_array($serviceSubClass, [ShippingService::FOX_ST_COURIER, ShippingService::FOX_EX_COURIER])) {
            return $this->foxMasterBox($container);
        } elseif (in_array($serviceSubClass, [ShippingService::PHX_ST_COURIER, ShippingService::PHX_EX_COURIER])) {
            return $this->phxMasterBox($container);
        } elseif (in_array($serviceSubClass, [ShippingService::ID_Label_Service])) {
            return $this->handleIdService($container);
        } else {
            return $this->defaultMasterBox($container);
        }
        session()->flash('alert-success', 'registered successfully!');
        return back();
    }
    function foxMasterBox($container)
    {
        $container->update([
            'unit_code' => 'HDFOX' . $this->containerId . 'BR',
            'response' => true,
        ]);
        session()->flash('alert-success', 'registered successfully!');
        return back();
    }
    function phxMasterBox($container)
    {
        $container->update([
            'unit_code' => 'HDPHX' . $this->containerId . 'BR',
            'response' => true,
        ]);
        session()->flash('alert-success', 'registered successfully!');
        return back();
    }
    function handleIdService($container)
    {
        $unitCodeSuffix = substr(optional($container->orders()->first())->corrios_tracking_code, -2) ?? 'GT';
        $container->update([
            'unit_code' =>  'HDC' . $this->containerId . $unitCodeSuffix,
            'response' => true,
        ]);
        session()->flash('alert-success', 'registered successfully!');
        return back();
    }
    function defaultMasterBox($container)
    {
        $container->update([
            'unit_code' => 'HDC' . $this->containerId . 'CO',
            'response' => true,
        ]);
        session()->flash('alert-success', 'registered successfully!');
        return back();
    }

    public function consultMasterBox(Request $request)
    {
        $container = Container::find($request->id);
        if ($container->unit_response_list) {
            $apiRequest = (new TotalExpressMasterBox($container))->consultCreateMasterBox($container);

            $response = $apiRequest->getData();
            if ($response->isSuccess) {
                session()->flash('alert-success', $response->message);
                return back();
            } else {
                session()->flash('alert-danger', $response->message);
                return back();
            }
        } else {
            session()->flash('alert-danger', 'Request ID Not Found');
            return back();
        }
    }
}
