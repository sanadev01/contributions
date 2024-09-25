<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\PasarEx\CN35LabelMaker;
use App\Services\FoxCourier\CN35LabelMaker as FoxCN35;
use Carbon\Carbon;
use App\Services\Cainiao\CN35LabelMaker as CainiaoCN35LabelMaker;
class CN35DownloadFactoryController extends Controller
{
    public $container;
    public function __invoke(Container $container)
    {
        $this->container = $container;
        if ($container->hasPasarExService()) {
            return $this->getPasarExLabel();
        }       
        if($container->has_cainiao){ 
            $cn23Maker = new CainiaoCN35LabelMaker($this->container);
            return $cn23Maker->download();
        }
        if($container->hasFoxCourierService()){
            return $this->getFoxCourierLabel();
        }
        session()->flash('alert-danger','We are not handle this container cn35 yet');
        return back();
    }

    function getPasarExLabel(){
        $cn23Maker = new CN35LabelMaker($this->container);
        $cn23Maker =   $cn23Maker->setDispatchNumber($this->container->dispatch_number)
            ->setDestinationAirport('GRU')
            ->setOriginAirport('MIA')
            ->setCompanyName('PasarEx')
            ->setDispatchDate(Carbon::now()->format('Y-m-d'));
        return $cn23Maker->download();
    }

    function getFoxCourierLabel(){
        $cn35Maker = new FoxCN35($this->container);
        $cn35Maker =   $cn35Maker->setDispatchNumber($this->container->dispatch_number)
            ->setDestinationAirport('GRU')
            ->setOriginAirport('MIA')
            ->setCompanyName('Fox Courier')
            ->setDispatchDate(Carbon::now()->format('Y-m-d'));
        return $cn35Maker->download();
    }
    
}
