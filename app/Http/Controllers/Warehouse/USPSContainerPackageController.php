<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\ContainerOrderExport;
use App\Repositories\Warehouse\UspsContainerPackageRepository;

class USPSContainerPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        $ordersCollection = json_encode($container->getOrdersCollections());
        $editMode = ($container->response == 0) ? true : false;

        return view('admin.warehouse.uspsContainers.scan',compact('container', 'ordersCollection', 'editMode'));
    }
    
}
