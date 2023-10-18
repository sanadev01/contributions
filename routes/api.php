<?php

use App\Models\Deposit;
use App\Models\Order;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

// Route for getting chile regions from correios chile api
Route::get('orders/recipient/chile_regions', [App\Http\Controllers\Api\Order\RecipientController::class, 'chileRegions'])->name('api.orders.recipient.chile_regions');
Route::get('orders/recipient/chile_communes', [App\Http\Controllers\Api\Order\RecipientController::class, 'chileCommunes'])->name('api.orders.recipient.chile_comunes');
Route::get('orders/recipient/normalize_address', [App\Http\Controllers\Api\Order\RecipientController::class, 'normalizeAddress'])->name('api.orders.recipient.normalize_address');

// Route for getting chile regions from db
Route::get('orders/recipient/hd_chile_regions', [App\Http\Controllers\Api\Order\RecipientController::class, 'hdChileRegions'])->name('api.orders.recipient.hd_chile_regions');
Route::get('orders/recipient/hd_chile_comunes', [App\Http\Controllers\Api\Order\RecipientController::class, 'hdChileCommunes'])->name('api.orders.recipient.hd_chile_comunes');

// Routes for usps
Route::get('orders/recipient/us_address', [App\Http\Controllers\Api\Order\RecipientController::class, 'validate_USAddress'])->name('api.orders.recipient.us_address');
Route::get('order-usps-rates', [App\Http\Controllers\Admin\Order\OrderItemsController::class, 'uspsRates'])->name('api.usps_rates');
Route::get('order-usps-sender-rates', [App\Http\Controllers\Admin\Order\OrderUSPSLabelController::class, 'usps_sender_rates'])->name('api.usps_sender_rates');

// Rates for UPS
Route::get('order-ups-rates', [App\Http\Controllers\Admin\Order\OrderItemsController::class, 'ups_rates'])->name('api.ups_rates');
Route::get('order-ups-sender-rates', [App\Http\Controllers\Admin\Order\OrderUPSLabelController::class, 'ups_sender_rates'])->name('api.ups_sender_rates');

// Rates for FedEx
Route::get('order-fedex-rates', [App\Http\Controllers\Admin\Order\OrderItemsController::class, 'fedExRates'])->name('api.fedExRates');

//Route for GSS
Route::get('order-gss-rates', [App\Http\Controllers\Admin\Order\OrderItemsController::class, 'GSSRates'])->name('api.gssRates');

Route::post('update/inventory-order', Api\InventoryOrderUpdateController::class)->name('api.inventory.order.update');

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
            Route::get('deposits',DepositController::class);
            Route::get('balance', BalanceController::class);
            Route::resource('parcels', 'ParcelController')->only('store','show','destroy','update');
            Route::get('parcel/{order}/cn23',OrderLabelController::class);
            Route::put('parcel/items/{parcel}',[App\Http\Controllers\Api\PublicApi\ParcelController::class, 'updateItems']);
            Route::match(['get', 'post'], 'order/tracking/{search?}/{format?}', [App\Http\Controllers\Api\PublicApi\OrderTrackingController::class, '__invoke']);
            Route::get('services-rates', GetRateController::class);
            Route::resource('products', 'ProductController')->only('index', 'show', 'store');
            Route::get('api/token', AmazonApiTokenController::class);
            Route::get('profile', ProfileController::class);
            Route::post('us/label',DomesticLabelController::class);
            Route::get('us/calculator',DomesticLabelRateController::class);
            Route::get('calculator', [App\Http\Controllers\Api\PublicApi\CalculatorController::class,'index']);
            Route::get('status/{order}', StatusController::class);
            Route::get('cancel/{order}', CancelOrderController::class);
            Route::get('get/tracking', TrackingController::class);
           //Cancel Lable Route for GePS
            Route::get('cancel-label/{order}', [App\Http\Controllers\Api\PublicApi\OrderLabelController::class, 'cancelGePSLabel']);
            Route::post('order/shiptrack/tracking', [App\Http\Controllers\Api\PublicApi\ShipTrackController::class, '__invoke']);
        });
    
        Route::get('countries', CountryController::class);
        Route::get('country/{country}/states', StateController::class);
        Route::get('shipping-services/{country_code?}', ServicesController::class);
        Route::get('shcodes/{search?}', ShCodeController::class);
    });
    Route::get('refund-tracking-orders', function (Request $request) {
    $orders = Order::whereIn('corrios_tracking_code', $request->trackings)->where('is_paid', true)->where('status', 70)->get();
        $countRefunded = 0;
        foreach ($orders as $order) {
            try {
                DB::beginTransaction();
                $deposit = Deposit::create([
                    'uuid' => PaymentInvoice::generateUUID('DP-'),
                    'amount' => $order->gross_total,
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'last_four_digits' => 'credit from cancelation, refund ' . $order->warehouse_number,
                    'balance' => Deposit::getCurrentBalance($order->user) + $order->gross_total,
                    'is_credit' => true,
                ]);
                 $order->deposits()->sync($deposit->id);
                $order->update([
                    'status'  => Order::STATUS_REFUND,
                    'is_paid' => false
                ]);
                $countRefunded++;
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'error' => $e->getMessage(),
                    'refunded' => $countRefunded,
                    'failed' => $orders->count() - $countRefunded
                ]);
            }
        }
        return response()->json([
            'status' => true,
            'refunded' => $countRefunded,
            'failed' => $orders->count() - $countRefunded
        ]);
    });

});