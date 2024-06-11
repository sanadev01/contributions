<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Warehouse\Container;
use App\Services\Excel\Export\ExportSinerlogManfestService;
use Illuminate\Http\Request;

class SinerlogManifestDownloadController extends Controller
{
    public function __invoke(Container $container)
    {
        $exportService = new ExportSinerlogManfestService($container);
        return $exportService->handle();
    }
}
