<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use App\Services\Excel\Export\BulkContainerExport;

class BulkContainerDownloadController extends Controller
{
    function index(Request $request)
    {
        $jsonString = $request->query('data');

        $containerIds = json_decode($jsonString, true);
        $containers = Container::whereIn('id', $containerIds)->get();
        $export = new BulkContainerExport($containers);
        $export->handle();
        return $export->download();
    }
}
