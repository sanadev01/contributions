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
use App\Http\Controllers\Warehouse\SearchPackageController;
use App\Http\Controllers\Warehouse\ContainerPackageController;
use App\Http\Controllers\Warehouse\ManifestDownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillDownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillRegisterController;
use App\Http\Controllers\Warehouse\DeliveryBillStatusUpdateController;


Route::middleware(['auth'])->as('warehouse.')->group(function () {

    Route::get('order/{order}/download-cn23', CN23DownloadController::class)->name('cn23.download');

    Route::resource('search_package', SearchPackageController::class)->only('index', 'show');
    
    Route::resource('containers', ContainerController::class);
    Route::get('awb/', AwbController::class)->name('container.awb');
    Route::resource('containers.packages', ContainerPackageController::class)->only('index','destroy', 'create');
    Route::post('containers/{container}/packages/{barcode}', [ContainerPackageController::class,'store'])->name('containers.packages.store');

    Route::get('container/{container}/register', UnitRegisterController::class)->name('container.register');
    Route::get('container/{container}/download', CN35DownloadController::class)->name('container.download');

    Route::resource('delivery_bill', DeliveryBillController::class);
    Route::get('delivery_bill/{delivery_bill}/register', DeliveryBillRegisterController::class)->name('delivery_bill.register');
    Route::get('delivery_bill/{delivery_bill}/status/refresh', DeliveryBillStatusUpdateController::class)->name('delivery_bill.status.refresh');
    Route::get('delivery_bill/{delivery_bill}/download', DeliveryBillDownloadController::class)->name('delivery_bill.download');
    Route::get('delivery_bill/{delivery_bill}/manifest', ManifestDownloadController::class)->name('delivery_bill.manifest');

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
