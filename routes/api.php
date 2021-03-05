<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('orders/recipient/update', Api\Order\RecipientController::class)->name('api.orders.recipient.update');

Route::post('order/update/status',Api\OrderStatusController::class)->name('api.order.status.update');

Route::prefix('v1')->middleware('auth:api')->group(function(){
    
    Route::namespace('Api\Warehouse')->group(function () {
    
        Route::resource('shipments', 'PackageConfirmationController')->only('store');
    
    });

    Route::post('files','Api\ImageUploaderController@store');

});


Route::prefix('v1')->group(function(){
    
    Route::namespace('Api\PublicApi')->group(function () {
    
        // Authenticated Routes
        Route::middleware(['auth:api','checkPermission'])->group(function (){
            Route::get('balance', BalanceController::class);
            Route::resource('parcels', 'ParcelController')->only('store','destroy','update');
            Route::get('parcel/{order}/cn23',OrderLabelController::class);
        });
    
        Route::get('countries', CountryController::class);
        Route::get('country/{country}/states', StateController::class);
        Route::get('shipping-services', ServicesController::class);
        Route::get('shcodes/{search}', ShCodeController::class);
    });


});