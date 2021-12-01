<?php

use App\Models\Order;
use Illuminate\Support\Facades\Route;
use App\Services\Correios\Models\Package;
use App\Http\Controllers\Warehouse\AwbController;
use App\Http\Controllers\Warehouse\ContainerController;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;
use App\Http\Controllers\Warehouse\CN23DownloadController;
use App\Http\Controllers\Warehouse\CN35DownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillController;
use App\Http\Controllers\Warehouse\UnitRegisterController;
use App\Http\Controllers\Warehouse\UnitCancelContoller;
use App\Http\Controllers\Warehouse\SearchPackageController;
use App\Http\Controllers\Warehouse\ScanPackageController;
use App\Http\Controllers\Warehouse\USPSContainerController;
use App\Http\Controllers\Warehouse\ChileContainerController;
use App\Http\Controllers\Warehouse\ContainerPackageController;
use App\Http\Controllers\Warehouse\ManifestDownloadController;
use App\Http\Controllers\Warehouse\USPSCN35DownloadController;
use App\Http\Controllers\Warehouse\USPSUnitRegisterController;
use App\Http\Controllers\Warehouse\ChileCN35DownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillDownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillRegisterController;
use App\Http\Controllers\Warehouse\USPSContainerPackageController;
use App\Http\Controllers\Warehouse\ChileContainerPackageController;
use App\Http\Controllers\Warehouse\DeliveryBillStatusUpdateController;
use App\Http\Controllers\Warehouse\SinerlogContainerController;
use App\Http\Controllers\Warehouse\SinerlogContainerPackageController;
use App\Http\Controllers\Warehouse\SinerlogUnitRegisterController;
use App\Http\Controllers\Warehouse\SinerlogCN35DownloadController;
use App\Http\Controllers\Warehouse\SinerlogManifestDownloadController;


Route::middleware(['auth'])->as('warehouse.')->group(function () {

    Route::get('order/{order}/download-cn23', CN23DownloadController::class)->name('cn23.download');

    Route::resource('search_package', SearchPackageController::class)->only('index', 'show');
    Route::resource('scan', ScanPackageController::class)->only('index');
    
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
    Route::get('delivery_bill/{delivery_bill}/download', DeliveryBillDownloadController::class)->name('delivery_bill.download');
    Route::get('delivery_bill/{delivery_bill}/manifest', ManifestDownloadController::class)->name('delivery_bill.manifest');

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
});


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
