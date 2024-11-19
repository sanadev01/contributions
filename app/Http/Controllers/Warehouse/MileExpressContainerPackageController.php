<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;

class MileExpressContainerPackageController extends Controller
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

        return view('admin.warehouse.mileExpressContainer.scan',compact('container', 'ordersCollection', 'editMode'));
    }
}
