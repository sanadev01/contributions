<?php
namespace App\Http\Controllers\Warehouse;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Hound\CN35LabelMaker;
class HoundCN35DownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Container $container)
    {
        $cn35Maker = new CN35LabelMaker($container);
        return $cn35Maker->download();        
    }
}
