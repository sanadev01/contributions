<?php

namespace App\Http\Livewire\SwedenPostContainer;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderTracking;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Warehouse\SwedenPostContainerPackageController;
use App\Repositories\Warehouse\SwedenPostContainerPackageRepository;
use App\Services\SwedenPost\Services\Container\DirectLinkReceptacle;
use Illuminate\Support\Facades\DB;

class Package extends Component
{
    
    public $container;
    public $idContainer;
    public $orders;
    public $editMode;
    public $tracking;
    public $error = null;
    public $num_of_Packages = 0;
    protected $rules = [
        'tracking' => 'required',
    ];

    public function mount($id = null, $editMode = null)
    {
        $this->container = Container::find($id);
        $this->idContainer = $id;
        $this->error = null;
        $this->emit('scanFocus');
        $this->editMode = $editMode;
    }

    public function render()
    {
        $this->dispatchBrowserEvent('scan-focus');
        $this->tracking = null; 
        return view('livewire.sweden-post-container.package', [
            'orders' => $this->getPackages($this->idContainer),
            'totalweight' => $this->totalWeight(),
            'num_of_Packages' => $this->totalPackages()
        ]);
    }

    public function getPackages($id)
    {
        $container = Container::find($id);
        return $this->orders = $container->orders;
    }

    public function submit()
    {
        $this->validate(); 
        $order = Order::where('corrios_tracking_code', $this->tracking)->first();
        if ($order) {
            $container = Container::find($this->idContainer);
            $swedenpost_containerPackageRepository = new SwedenPostContainerPackageRepository();

            $response =  $swedenpost_containerPackageRepository->addOrderToPackageContainer($container, $order);


            if (!$response['success']) {
                return $this->error = $response['message'];
            }
            $this->error = null;
            return;
        }
        $this->error = "Order Not found please check tracking code: $this->tracking";
    }

    public function removeOrder(Order $order)
    { 
        $swedenpost_containerPackageRepository = new SwedenPostContainerPackageRepository();
        return $swedenpost_containerPackageRepository->removeOrderFromPackageContainer($this->container, $order->id);
    }

    public function totalPackages()
    {
        return  $this->num_of_Packages = count($this->orders);
    }

    public function totalWeight()
    {
        $orders = $this->container->orders();
        return $orders->selectRaw('sum(CASE WHEN measurement_unit = "kg/cm" THEN ROUND(weight,2) ELSE ROUND((weight/2.205),2) END) as weight')->first()->weight;
    }

}