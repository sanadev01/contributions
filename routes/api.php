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


Route::post('orders/recipient/update', [App\Http\Controllers\Api\Order\RecipientController::class, 'update'])->name('api.orders.recipient.update');
Route::get('orders/recipient/zipcode', [App\Http\Controllers\Api\Order\RecipientController::class, 'zipcode'])->name('api.orders.recipient.zipcode');

Route::get('orders/recipient/chile_regions', [App\Http\Controllers\Api\Order\RecipientController::class, 'chileRegions'])->name('api.orders.recipient.chile_regions');
Route::get('orders/recipient/chile_communes', [App\Http\Controllers\Api\Order\RecipientController::class, 'chileCommunes'])->name('api.orders.recipient.chile_comunes');
Route::get('orders/recipient/normalize_address', [App\Http\Controllers\Api\Order\RecipientController::class, 'normalizeAddress'])->name('api.orders.recipient.normalize_address');

// Routes for usps
Route::get('orders/recipient/us_address', [App\Http\Controllers\Api\Order\RecipientController::class, 'validate_USAddress'])->name('api.orders.recipient.us_address');
Route::get('order-usps-rates', [App\Http\Controllers\Admin\Order\OrderItemsController::class, 'usps_rates'])->name('api.usps_rates');

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
        Route::get('shcodes/{search?}', ShCodeController::class);
        Route::get('order/tracking/{search}', OrderTrackingController::class);
        Route::get('services-rates', GetRateController::class);
    });


});