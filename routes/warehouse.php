<?php

use App\Http\Controllers\Warehouse\CN23DownloadController;
use App\Http\Controllers\Warehouse\CN35DownloadController;
use App\Http\Controllers\Warehouse\ContainerController;
use App\Http\Controllers\Warehouse\ContainerPackageController;
use App\Http\Controllers\Warehouse\DeliveryBillController;
use App\Http\Controllers\Warehouse\DeliveryBillDownloadController;
use App\Http\Controllers\Warehouse\DeliveryBillRegisterController;
use App\Http\Controllers\Warehouse\DeliveryBillStatusUpdateController;
use App\Http\Controllers\Warehouse\UnitRegisterController;
use App\Models\Order;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Contracts\PacketItem;
use App\Services\Correios\Models\Package;
use App\Services\Correios\Services\Brazil\Client;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;
use App\Services\Correios\Services\Brazil\CN35LabelMaker;
use App\Services\Correios\Services\Brazil\CN38LabelMaker;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->as('warehouse.')->group(function () {

    Route::get('order/{order}/download-cn23', CN23DownloadController::class)->name('cn23.download');
    Route::resource('containers', ContainerController::class);
    Route::resource('containers.packages', ContainerPackageController::class)->only('index','destroy');
    Route::post('containers/{container}/packages/{barcode}', [ContainerPackageController::class,'store'])->name('containers.packages.store');

    Route::get('container/{container}/register', UnitRegisterController::class)->name('container.register');
    Route::get('container/{container}/download', CN35DownloadController::class)->name('container.download');

    Route::resource('delivery_bill', DeliveryBillController::class);
    Route::get('delivery_bill/{delivery_bill}/register', DeliveryBillRegisterController::class)->name('delivery_bill.register');
    Route::get('delivery_bill/{delivery_bill}/status/refresh', DeliveryBillStatusUpdateController::class)->name('delivery_bill.status.refresh');
    Route::get('delivery_bill/{delivery_bill}/download', DeliveryBillDownloadController::class)->name('delivery_bill.download');

});

// Route::get('test-pdf', function () {
//     $labelPrinter = new LabelMaker;
//     $labelPrinter->setCorrieosLogo();
//     $labelPrinter->setPartnerLogo();
//     $labelPrinter->setServiceLogo();

//     $recipient = new Recipient;
//     $recipient->recipientName = 'Admin';
//     $recipient->recipientPhoneNumber = '+8156912324';
//     $recipient->recipientAddress = 'hiad, streest 5 ,aasdfhsd';
//     $recipient->recipientZipCode = '912324';
//     $recipient->recipientState = 'SP';
//     $labelPrinter->setDestination($recipient);

//     $labelPrinter->download();
// });

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


    $order = Order::find(53654);

    $client = new Client();


    echo $client->createPackage($order);

});
