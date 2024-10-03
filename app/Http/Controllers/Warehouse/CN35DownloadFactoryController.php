<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\PasarEx\CN35LabelMaker;
use App\Services\SmartComex\CN35LabelMaker as SmartComexCN35;
use Carbon\Carbon;
class CN35DownloadFactoryController extends Controller
{
    public $container;
    public function __invoke(Container $container)
    {
        $this->container = $container;
        if($container->hasPasarExService()){
            return $this->getPasarExLabel();
        }
        if($container->hasFoxCourierService()){
            return $this->getFoxCourierLabel();
        }
        if($container->hasPhxCourierService()){
            return $this->getPhxCourierLabel();
        }
        session()->flash('alert-danger','We are not handle this container cn35 yet');
        return back();
    }

    function getPasarExLabel(){
        $cn23Maker = new CN35LabelMaker($this->container);
        $packetType = 'PasarEx';
        $cn23Maker =   $cn23Maker->setDispatchNumber($this->container->dispatch_number)
            ->setDestinationAirport('GRU')
            ->setOriginAirport('MIA')
            ->setCompanyName('PasarEx')
            ->setDispatchDate(Carbon::now()->format('Y-m-d'));
        return $cn23Maker->download();
    }

    function getFoxCourierLabel(){
        $cn35Maker = new SmartComexCN35($this->container);
        $cn35Maker =   $cn35Maker->setDispatchNumber($this->container->dispatch_number)
            ->setDestinationAirport('GRU')
            ->setOriginAirport('MIA')
            ->setCompanyName('Fox Courier')
            ->setDispatchDate(Carbon::now()->format('Y-m-d'));
        return $cn35Maker->download();
    }

    function getPhxCourierLabel(){
        $cn35Maker = new SmartComexCN35($this->container);
        $cn35Maker =   $cn35Maker->setDispatchNumber($this->container->dispatch_number)
            ->setDestinationAirport('GRU')
            ->setOriginAirport('MIA')
            ->setCompanyName('Phx Courier')
            ->setDispatchDate(Carbon::now()->format('Y-m-d'));
        return $cn35Maker->download();
    }
}