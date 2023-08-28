<?php

use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Services\Excel\Export\OrderExportTemp;
use App\Http\Controllers\Warehouse\AwbController;
use App\Http\Controllers\Warehouse\ContainerController;
use App\Http\Controllers\Warehouse\ScanLabelController;
use App\Http\Controllers\Warehouse\UnitCancelContoller;
use App\Http\Controllers\Warehouse\AuditReportController;
use App\Http\Controllers\Warehouse\ScanPackageController;
use App\Http\Controllers\Warehouse\CN23DownloadController;
use App\Http\Controllers\Warehouse\CN35DownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillController;
use App\Http\Controllers\Warehouse\UnitRegisterController;

use App\Http\Controllers\Warehouse\Anjun\AnjunUnitRegisterController;
use App\Http\Controllers\Warehouse\Anjun\AnjunCN35DownloadController;
use App\Http\Controllers\Warehouse\SearchPackageController;
use App\Http\Controllers\Warehouse\USPSContainerController;
use App\Http\Controllers\Warehouse\ChileContainerController;
use App\Http\Controllers\Warehouse\ContainerPackageController;
use App\Http\Controllers\Warehouse\ManifestDownloadController;
use App\Http\Controllers\Warehouse\USPSCN35DownloadController;
use App\Http\Controllers\Warehouse\USPSUnitRegisterController;
use App\Http\Controllers\Warehouse\ChileCN35DownloadController;
use App\Http\Controllers\Warehouse\SinerlogContainerController;
use App\Http\Controllers\Warehouse\DeliveryBillDownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillRegisterController;
use App\Http\Controllers\Warehouse\MileExpressContainerController;
use App\Http\Controllers\Warehouse\SinerlogCN35DownloadController;
use App\Http\Controllers\Warehouse\SinerlogUnitRegisterController;
use App\Http\Controllers\Warehouse\USPSContainerPackageController;
use App\Http\Controllers\Warehouse\USPSManifestDownloadController;
use App\Http\Controllers\Warehouse\ChileContainerPackageController;
use App\Http\Controllers\Warehouse\ChileManifestDownloadController;
use App\Http\Controllers\Warehouse\CombineManifestDownloadController;
use App\Http\Controllers\Warehouse\MileExpressUnitRegisterController;
use App\Http\Controllers\Warehouse\DeliveryBillStatusUpdateController;
use App\Http\Controllers\Warehouse\SinerlogContainerPackageController;
use App\Http\Controllers\Warehouse\SinerlogManifestDownloadController;
use App\Http\Controllers\Warehouse\MileExpressContainerPackageController;
use App\Http\Controllers\Warehouse\GePSContainerController;
use App\Http\Controllers\Warehouse\GePSContainerPackageController;
use App\Http\Controllers\Warehouse\GePSUnitRegisterController;
use App\Http\Controllers\Warehouse\GePSCN35DownloadController;
use App\Http\Controllers\Warehouse\GePSManifestDownloadController;
use App\Http\Controllers\Warehouse\UnitsInfoController;
use App\Http\Controllers\Warehouse\SwedenPostContainerController;
use App\Http\Controllers\Warehouse\SwedenPostContainerPackageController;
use App\Http\Controllers\Warehouse\SwedenPostUnitRegisterController;
use App\Http\Controllers\Warehouse\SwedenPostCN35DownloadController;
use App\Http\Controllers\Warehouse\SwedenPostManifestDownloadController;
use App\Http\Controllers\Warehouse\PostPlusContainerController;
use App\Http\Controllers\Warehouse\PostPlusContainerPackageController;
use App\Http\Controllers\Warehouse\PostPlusUnitPrepareController;
use App\Http\Controllers\Warehouse\PostPlusUnitRegisterController;
use App\Http\Controllers\Warehouse\PostPlusCN35DownloadController;
use App\Http\Controllers\Warehouse\PostPlusCN38DownloadController;
use App\Http\Controllers\Warehouse\PostPlusManifestDownloadController;
use App\Http\Controllers\Warehouse\ColombiaContainerController;
use App\Http\Controllers\Warehouse\ColombiaCN35DownloadController;
use App\Http\Controllers\Warehouse\ColombiaUnitRegisterController;
use App\Http\Controllers\Warehouse\ColombiaContainerPackageController;
use App\Http\Controllers\Warehouse\ColombiaContainerManifestController;
use App\Http\Controllers\Warehouse\MilliExpressContainerController;
use App\Http\Controllers\Warehouse\POSTNLContainerController;
use App\Http\Controllers\Warehouse\POSTNLContainerPackageController;
use App\Http\Controllers\Warehouse\POSTNLUnitRegisterController;
use App\Http\Controllers\Warehouse\POSTNLCN35DownloadController;
use App\Http\Controllers\Warehouse\GDEContainerController;
use App\Http\Controllers\Warehouse\GDEContainerPackageController;
use App\Http\Controllers\Warehouse\GDEUnitRegisterController;
use App\Http\Controllers\Warehouse\GDECN35DownloadController;
use App\Http\Controllers\Warehouse\GDEManifestDownloadController;
use App\Http\Controllers\Warehouse\TotalExpressContainerController;
use App\Http\Controllers\Warehouse\TotalExpressContainerPackageController;
use App\Http\Controllers\Warehouse\TotalExpressUnitRegisterController;
use App\Http\Controllers\Warehouse\TotalExpressCN35DownloadController;
use App\Http\Controllers\Warehouse\GSSContainerController;
use App\Http\Controllers\Warehouse\GSSContainerPackageController;
use App\Http\Controllers\Warehouse\GSSUnitRegisterController;
use App\Http\Controllers\Warehouse\GSSCN35DownloadController;
use App\Http\Controllers\Warehouse\GSSCN38DownloadController;
use App\Http\Controllers\Warehouse\GSSManifestDownloadController;
use App\Http\Controllers\Warehouse\GSSReportsDownloadController;


Route::middleware(['auth'])->as('warehouse.')->group(function () {

    Route::get('order/{order}/download-cn23', CN23DownloadController::class)->name('cn23.download');

    Route::resource('search_package', SearchPackageController::class)->only('index', 'show');
    Route::resource('scan', ScanPackageController::class)->only('index');
    Route::resource('scan-label', ScanLabelController::class)->only('index', 'store', 'create');

    Route::resource('containers', ContainerController::class);

    Route::get('awb/', AwbController::class)->name('container.awb');
    Route::resource('containers.packages', ContainerPackageController::class)->only('index','destroy', 'create');
    Route::post('containers/{container}/packages/{barcode}', [ContainerPackageController::class,'store'])->name('containers.packages.store');

    Route::get('anjun/container/{container}/register', AnjunUnitRegisterController::class)->name('anjun.container.register');
    Route::get('anjun/container/{container}/download', AnjunCN35DownloadController::class)->name('anjun.container.download');


    Route::get('container/{container}/register', UnitRegisterController::class)->name('container.register');
    Route::get('container/{container}/cancel', UnitCancelContoller::class)->name('container.cancel');
    Route::get('container/{container}/download', CN35DownloadController::class)->name('container.download');
    Route::get('container/hd-express/{container}/download', MilliExpressContainerController::class)->name('hd-express.container.download');
    
    Route::resource('delivery_bill', DeliveryBillController::class);
    Route::get('delivery_bill/{delivery_bill}/register', DeliveryBillRegisterController::class)->name('delivery_bill.register');
    Route::get('delivery_bill/{delivery_bill}/status/refresh', DeliveryBillStatusUpdateController::class)->name('delivery_bill.status.refresh');
    Route::resource('delivery_bill/download', DeliveryBillDownloadController::class)->only('show', 'create');
    // Route::get('delivery_bill/{delivery_bill}/download', DeliveryBillDownloadController::class)->name('delivery_bill.download');
    Route::get('delivery_bill/{delivery_bill}/manifest', ManifestDownloadController::class)->name('delivery_bill.manifest');

    Route::post('combine-delivery-bill/manifest/download', CombineManifestDownloadController::class)->name('combine_delivery_bill.manifest.download');
    
    Route::resource('audit-report', AuditReportController::class)->only(['show']);

    // ALL Routes for Chile Containers
    Route::resource('chile_containers', ChileContainerController::class);
    Route::get('chile-container/{container}/packages', ChileContainerPackageController::class)->name('chile-container.packages');
    Route::get('chile_container/{container}/download_txt_manifest', [ChileContainerController::class, 'download_txtManifest'])->name('download.manifest_txt');
    Route::get('chile_container/{container}/download_excel_manifest', [ChileContainerController::class, 'download_exceltManifest'])->name('download.manifest_excel');
    Route::get('chile_container/{container?}/download_combine_manifest', [ChileContainerController::class, 'download_combine_manifest'])->name('download.combine_manifest');
    Route::get('chile_container/{container}/upload_manifest', [ChileContainerController::class, 'upload_ManifestToChile'])->name('upload.manifest');
    Route::get('chile_container/{container}/download_chile_cn35', ChileCN35DownloadController::class)->name('download.chile_cn35');
    Route::get('chile-container/{container}/manifest', ChileManifestDownloadController::class)->name('chile-container.manifest');

    // Routes for USPS Container
    Route::resource('usps_containers', USPSContainerController::class);
    // Route::resource('usps_container.packages', USPSContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('usps_container/{container}/packages', USPSContainerPackageController::class)->name('usps-container.packages');
    Route::get('uspscontainer/{container}/register', USPSUnitRegisterController::class)->name('usps_container.register');
    Route::get('usps_container/{container}/download', USPSCN35DownloadController::class)->name('usps_container.download');
    Route::get('usps_container/{container}/download_excel_manifest', [USPSContainerController::class, 'download_exceltManifest'])->name('download.usps_manifest_excel');
    Route::get('usps-container/{container}/manifest', USPSManifestDownloadController::class)->name('usps-container.manifest');
    // Routes for Sinerlog Container
    Route::resource('sinerlog_containers', SinerlogContainerController::class);
    Route::resource('sinerlog_container.packages', SinerlogContainerPackageController::class)->only('index','destroy', 'create');
    Route::post('sinerlog_container/{container}/packages/{barcode}', [SinerlogContainerPackageController::class,'store'])->name('sinerlog_container.packages.store');
    Route::get('sinerlog_container/{container}/register', SinerlogUnitRegisterController::class)->name('sinerlog_container.register');
    Route::get('sinerlog_container/{container}/download', SinerlogCN35DownloadController::class)->name('sinerlog_container.download');
    Route::get('sinerlog_container/{container}/manifest', SinerlogManifestDownloadController::class)->name('sinerlog_container.manifest');

    // Routes for Homedelivebr Express container
    Route::resource('mile-express-containers', MileExpressContainerController::class);
    Route::get('mile-express-container/{container}/packages', MileExpressContainerPackageController::class)->name('mile-express-container.packages');
    Route::get('mile-express-container/{container}/register', MileExpressUnitRegisterController::class)->name('mile-express-container.register');
    // Routes for POSTNL Container
    Route::resource('postnl_containers', POSTNLContainerController::class);
    Route::resource('postnl_container.packages', POSTNLContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('postnl_container/{container}/register', POSTNLUnitRegisterController::class)->name('postnl_container.register');
    Route::get('postnl_container/{container}/download', POSTNLCN35DownloadController::class)->name('postnl_container.download');
    Route::get('postnl_container/{container}/download_excel_manifest', [POSTNLContainerController::class, 'download_exceltManifest'])->name('download.postnl_manifest_excel');

    // Routes for GePS Container
    Route::resource('geps_containers', GePSContainerController::class);
    Route::resource('geps_container.packages', GePSContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('geps_container/{container}/register', GePSUnitRegisterController::class)->name('geps_container.register');
    Route::get('geps_container/{container}/download', GePSCN35DownloadController::class)->name('geps_container.download');
    Route::get('geps/{delivery_bill}/manifest', GePSManifestDownloadController::class)->name('geps.manifest.download');
    Route::post('geps_container/{container}/upload-trackings', [\App\Http\Controllers\Warehouse\GePSContainerPackageController::class, 'uploadBulkTracking'])->name('upload-bulk-trackings');

    // Routes for Correios Unit Info
    Route::resource('unitinfo', UnitsInfoController::class);

    // Routes for Prime5 Container
    Route::resource('swedenpost_containers', SwedenPostContainerController::class);
    Route::resource('swedenpost_container.packages', SwedenPostContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('swedenpost_container/{container}/register', SwedenPostUnitRegisterController::class)->name('swedenpost_container.register');
    Route::get('swedenpost_container/{container}/download', SwedenPostCN35DownloadController::class)->name('swedenpost_container.download');
    Route::get('swedenpost/{delivery_bill}/manifest', SwedenPostManifestDownloadController::class)->name('swedenpost.manifest.download');

    // Routes for Post Plus Container
    Route::resource('postplus_containers', PostPlusContainerController::class);
    Route::resource('postplus_container.packages', PostPlusContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('postplus_container/{container}/prepare', PostPlusUnitPrepareController::class)->name('postplus_container.prepare');
    Route::get('postplus_container/{container}/register', PostPlusUnitRegisterController::class)->name('postplus_container.register');
    Route::get('postplus_container/{container}/download/', PostPlusCN35DownloadController::class)->name('postplus_container.download');
    Route::get('postplus/{delivery_bill}/cn38', PostPlusCN38DownloadController::class)->name('postplus.cn38.download');
    Route::get('postplus/{delivery_bill}/manifest', PostPlusManifestDownloadController::class)->name('postplus.manifest.download');
    // Routes for Colombia Container
     Route::resource('colombia-containers', ColombiaContainerController::class);
     Route::resource('colombia-containers.packages', ColombiaContainerPackageController::class)->only('index','destroy', 'create');
     Route::get('colombia-containers/{container}/register', ColombiaUnitRegisterController::class)->name('colombia-containers.register');
     Route::get('colombia-containers/{container}/download', ColombiaCN35DownloadController::class)->name('colombia-containers.download');
     Route::get('colombia-containers/{container}/manifest', ColombiaContainerManifestController::class)->name('colombia-containers.manifest');

     // Routes for GDE Container
    Route::resource('gde_containers', GDEContainerController::class);
    Route::resource('gde_container.packages', GDEContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('gde_container/{container}/register', GDEUnitRegisterController::class)->name('gde_container.register');
    Route::get('gde_container/{container}/download', GDECN35DownloadController::class)->name('gde_container.download');
    Route::get('gde/{delivery_bill}/manifest', GDEManifestDownloadController::class)->name('gde.manifest.download');

    // Routes for GSS Container
    Route::resource('gss_containers', GSSContainerController::class);
    Route::resource('gss_container.packages', GSSContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('gss_container/{container}/register', GSSUnitRegisterController::class)->name('gss_container.register');
    Route::get('gss_container/{container}/download/', GSSCN35DownloadController::class)->name('gss_container.download');
    Route::get('gss/{delivery_bill}/cn38', GSSCN38DownloadController::class)->name('gss_container.cn38.download');
    Route::get('gss/{delivery_bill}/manifest', GSSManifestDownloadController::class)->name('gss_container.manifest.download');
    Route::get('gss/{reports}/manifest/{dispatch}', GSSReportsDownloadController::class)->name('gss_container.reports.download');

    // Routes for Total Express Container
    Route::resource('totalexpress_containers', TotalExpressContainerController::class);
    Route::resource('totalexpress_container.packages', TotalExpressContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('totalexpress_container/{container}/register', TotalExpressUnitRegisterController::class)->name('totalexpress_container.register');
    Route::get('totalexpress_container/{container}/download', TotalExpressCN35DownloadController::class)->name('totalexpress_container.download');
    
    
});


Route::get('temp_manifest/{container}/download', function(Container $container){
    $orders = $container->orders;        
    $exportService = new OrderExportTemp($orders,Auth::id());
    $exportService->handle(); 
    return $exportService->download(); 
})->name('temp_manifest.download');
Route::get('test', function () {

    // $labelPrinter = new CN35LabelMaker;
    // $labelPrinter->setDispatchNumber(20793)
    //             ->setService(2)
    //             ->setDispatchDate('2021-01-22')
    //             ->setSerialNumber(1)
    //             ->setOriginAirport('MIA')
    //             ->setDestinationAirport('GRU')
    //             ->setWeight('187')
    //             ->setItemsCount(120)
    //             ->setUnitCode('USLEVEBRSAODANX10793001005172');


    // $order = Order::find(53654);

    // $client = new Client();


    // echo $client->createPackage($order);

    // $order = Order::find(53654);

    // $labelPrinter = new CN23LabelMaker();
    // $labelPrinter->setOrder($order);
    // $labelPrinter->setPacketType(Package::SERVICE_CLASS_STANDARD);
    // return $labelPrinter->download();

});
