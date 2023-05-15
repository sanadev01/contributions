<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;
use App\Services\Correios\Models\Package;
use App\Http\Controllers\Warehouse\AwbController;
use App\Http\Controllers\Warehouse\ContainerController;
use App\Http\Controllers\Warehouse\ScanLabelController;
use App\Http\Controllers\Warehouse\UnitCancelContoller;
use App\Http\Controllers\Warehouse\AuditReportController;
use App\Http\Controllers\Warehouse\ScanPackageController;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;
use App\Http\Controllers\Warehouse\CN23DownloadController;
use App\Http\Controllers\Warehouse\CN35DownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillController;
use App\Http\Controllers\Warehouse\UnitRegisterController;
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
use App\Http\Controllers\Warehouse\SinerlogCN35DownloadController;
use App\Http\Controllers\Warehouse\SinerlogUnitRegisterController;
use App\Http\Controllers\Warehouse\USPSContainerPackageController;
use App\Http\Controllers\Warehouse\ChileContainerPackageController;
use App\Http\Controllers\Warehouse\DeliveryBillStatusUpdateController;
use App\Http\Controllers\Warehouse\SinerlogContainerPackageController;
use App\Http\Controllers\Warehouse\SinerlogManifestDownloadController;
use App\Http\Controllers\Warehouse\CombineManifestDownloadController;
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
use App\Http\Controllers\Warehouse\PostPlusUnitRegisterController;
use App\Http\Controllers\Warehouse\PostPlusCN35DownloadController;
use App\Http\Controllers\Warehouse\PostPlusCN38DownloadController;
use App\Http\Controllers\Warehouse\PostPlusManifestDownloadController;
use App\Http\Controllers\Warehouse\GSSContainerController;
use App\Http\Controllers\Warehouse\GSSContainerPackageController;
use App\Http\Controllers\Warehouse\GSSUnitRegisterController;
use App\Http\Controllers\Warehouse\GSSCN35DownloadController;
use App\Http\Controllers\Warehouse\GSSCN38DownloadController;
use App\Http\Controllers\Warehouse\GSSManifestDownloadController;
use App\Models\Warehouse\Container;
use App\Services\Excel\Export\OrderExportTemp;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth'])->as('warehouse.')->group(function () {

    Route::get('order/{order}/download-cn23', CN23DownloadController::class)->name('cn23.download');

    Route::resource('search_package', SearchPackageController::class)->only('index', 'show');
    Route::resource('scan', ScanPackageController::class)->only('index');
    Route::resource('scan-label', ScanLabelController::class)->only('index', 'store', 'create');
    
    Route::resource('containers', ContainerController::class);
    Route::get('awb/', AwbController::class)->name('container.awb');
    Route::resource('containers.packages', ContainerPackageController::class)->only('index','destroy', 'create');
    Route::post('containers/{container}/packages/{barcode}', [ContainerPackageController::class,'store'])->name('containers.packages.store');
    
    Route::get('container/{container}/register', UnitRegisterController::class)->name('container.register');
    Route::get('container/{container}/cancel', UnitCancelContoller::class)->name('container.cancel');
    Route::get('container/{container}/download', CN35DownloadController::class)->name('container.download');
    
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
    Route::resource('chile_container.packages', ChileContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('chile_container/{container}/download_txt_manifest', [ChileContainerController::class, 'download_txtManifest'])->name('download.manifest_txt');
    Route::get('chile_container/{container}/download_excel_manifest', [ChileContainerController::class, 'download_exceltManifest'])->name('download.manifest_excel');
    Route::get('chile_container/{container?}/download_combine_manifest', [ChileContainerController::class, 'download_combine_manifest'])->name('download.combine_manifest');
    Route::get('chile_container/{container}/upload_manifest', [ChileContainerController::class, 'upload_ManifestToChile'])->name('upload.manifest');
    Route::get('chile_container/{container}/download_chile_cn35', ChileCN35DownloadController::class)->name('download.chile_cn35');
    
    // Routes for USPS Container
    Route::resource('usps_containers', USPSContainerController::class);
    Route::resource('usps_container.packages', USPSContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('usps_container/{container}/register', USPSUnitRegisterController::class)->name('usps_container.register');
    Route::get('usps_container/{container}/download', USPSCN35DownloadController::class)->name('usps_container.download');
    Route::get('usps_container/{container}/download_excel_manifest', [USPSContainerController::class, 'download_exceltManifest'])->name('download.usps_manifest_excel');

    // Routes for Sinerlog Container
    Route::resource('sinerlog_containers', SinerlogContainerController::class);
    Route::resource('sinerlog_container.packages', SinerlogContainerPackageController::class)->only('index','destroy', 'create');
    Route::post('sinerlog_container/{container}/packages/{barcode}', [SinerlogContainerPackageController::class,'store'])->name('sinerlog_container.packages.store');
    Route::get('sinerlog_container/{container}/register', SinerlogUnitRegisterController::class)->name('sinerlog_container.register');
    Route::get('sinerlog_container/{container}/download', SinerlogCN35DownloadController::class)->name('sinerlog_container.download');
    Route::get('sinerlog_container/{container}/manifest', SinerlogManifestDownloadController::class)->name('sinerlog_container.manifest');

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
    Route::get('postplus_container/{container}/register', PostPlusUnitRegisterController::class)->name('postplus_container.register');
    Route::get('postplus_container/{container}/download/', PostPlusCN35DownloadController::class)->name('postplus_container.download');
    Route::get('postplus/{delivery_bill}/cn38', PostPlusCN38DownloadController::class)->name('postplus.cn38.download');
    Route::get('postplus/{delivery_bill}/manifest', PostPlusManifestDownloadController::class)->name('postplus.manifest.download');

    // Routes for GSS Container
    Route::resource('gss_containers', GSSContainerController::class);
    Route::resource('gss_container.packages', GSSContainerPackageController::class)->only('index','destroy', 'create');
    Route::get('gss_container/{container}/register', GSSUnitRegisterController::class)->name('gss_container.register');
    Route::get('gss_container/{container}/download/', GSSCN35DownloadController::class)->name('gss_container.download');
    Route::get('gss/{delivery_bill}/cn38', GSSCN38DownloadController::class)->name('gss_container.cn38.download');
    Route::get('gss/{delivery_bill}/manifest', GSSManifestDownloadController::class)->name('gss_container.manifest.download');
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
