<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Correios\Services\Brazil\CN35LabelMaker;
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
        session()->flash('alert-danger','We are not handle this container cn35 yet');
        return back();
    }
    function getPasarExLabel(){
        $cn23Maker = new CN35LabelMaker($this->container);
        $packetType = 'PasarEx';
        $cn23Maker =   $cn23Maker->setDispatchNumber($this->container->dispatch_number)
            ->setDestinationAirport('GRU')
            ->setOriginAirport('MIA')
            ->setPacketType($packetType)
            ->setCompanyName('PasarEx')
            ->setDispatchDate(Carbon::now()->format('Y-m-d'));
        return $cn23Maker->download();
    }
}