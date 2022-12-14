<?php

use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;
use App\Services\StoreIntegrations\Shopify;
use App\Http\Controllers\Admin\HomeController;
use App\Services\Correios\Services\Brazil\Client;
use App\Http\Controllers\Admin\Deposit\DepositController;
use App\Services\Correios\Services\Brazil\CN23LabelMaker;
use App\Http\Controllers\Admin\Order\OrderUSLabelController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Shopify $shopifyClient) {
    $shop = "https://".request()->shop;
    if (request()->has('shop') ) {
        $redirectUri = $shopifyClient->getRedirectUrl(request()->shop,[
            'connect_name' => request()->shop,
            'connect_store_url' => $shop
        ]);
        return redirect()->away($redirectUri);
    }
    return redirect('login');
});

Route::resource('calculator', CalculatorController::class)->only(['index', 'store']);
Route::resource('us-calculator', USCalculatorController::class)->only(['index', 'store']);

// Route::resource('tracking', TrackingController::class)->only(['index', 'show']);
Route::get('/home', function () {

    if ( session()->get('shopify.redirect') ){
        return redirect(session()->get('shopify.redirect'));
    }

    return redirect()->route('admin.home');
});

Auth::routes();

Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class,'logout'])->name('logout');


Route::namespace('Admin')->middleware(['auth'])->as('admin.')->group(function () {

        Route::get('dashboard', 'HomeController')->name('home');
        Route::resource('parcels', PreAlertController::class);
        Route::get('parcel/{order}/duplicate',DuplicatePreAlertController::class)->name('parcel.duplicate');
        Route::resource('billing-information', BillingInformationController::class);
        // Route::resource('import-excel', ImportExcelController::class)->only(['index','store']);

        Route::resource('handling-services', HandlingServiceController::class)->except('show');
        Route::resource('addresses', AddressController::class);
        Route::get('addresses-export', [\App\Http\Controllers\Admin\AddressController::class, 'exportAddresses'])->name('export.addresses');
        Route::resource('shipping-services', ShippingServiceController::class);

        Route::namespace('Import')->prefix('import')->as('import.')->group(function () {
            Route::resource('import-excel', ImportExcelController::class)->only(['index','create','store','show','destroy']);
            Route::resource('import-order', ImportOrderController::class)->only(['index','store','show', 'edit','destroy']);
        });

        Route::resource('orders',OrderController::class)->only('index','destroy', 'show');
        Route::resource('trash-orders',TrashOrderController::class)->only(['index','destroy']);

        Route::resource('tracking', TrackingController::class)->only(['index', 'show']);
        Route::get('/buy-usps-label', [\App\Http\Controllers\Admin\Order\OrderUSPSLabelController::class, 'uspsBulkView'])->name('bulk-usps-label');

        Route::namespace('Order')->group(function () {
            Route::resource('leve-order-import', LeveOrderImportController::class)->only(['index','store']);
            Route::get('orders/{order}/duplicate',DuplicateOrderController::class)->name('orders.duplicate');
            Route::resource('orders.sender',OrderSenderController::class)->only('index','store');
            Route::resource('orders.recipient',OrderRecipientController::class)->only('index','store');
            Route::resource('orders.services',OrderServicesController::class)->only('index','store');
            Route::resource('orders.order-details', OrderItemsController::class)->only('index','store');
            Route::resource('orders.order-invoice', OrderInvoiceController::class)->only('index','store');
            Route::resource('orders.label', OrderLabelController::class)->only('index','store');
            Route::get('order-exports', OrderExportController::class)->name('order.exports');
            Route::get('bulk-action', BulkActionController::class)->name('order.bulk-action');
            Route::get('pre-alert', PreAlertMailController::class)->name('order.pre-alert');
            Route::get('consolidate-domestic-label', ConsolidateDomesticLabelController::class)->name('order.consolidate-domestic-label');
            Route::get('order/{order}/us-label', [OrderUSLabelController::class, 'index'])->name('order.us-label.index');
            Route::resource('orders.usps-label', OrderUSPSLabelController::class)->only('index','store');
            Route::resource('orders.ups-label', OrderUPSLabelController::class)->only('index','store');
            Route::get('order-ups-label-cancel-pickup/{id?}', [\App\Http\Controllers\Admin\Order\OrderUPSLabelController::class, 'cancelUPSPickup'])->name('order.ups-label.cancel.pickup');
        });
        //Cancel Lable Route for GePS
        Route::get('order/{order}/cancel-label', [\App\Http\Controllers\Admin\Order\OrderLabelController::class, 'cancelLabel'])->name('order.label.cancel');

        Route::namespace('Consolidation')->prefix('consolidation')->as('consolidation.')->group(function(){
            Route::resource('parcels',SelectPackagesController::class)->only('index','store','edit','update');
            Route::resource('parcels.services',ServicesController::class)->only('index','store');
        });


        Route::namespace('Payment')->group(function(){
            Route::resource('payment-invoices', PaymentInvoiceController::class)->only(['index','store','destroy']);
            Route::prefix('payment-invoices')->as('payment-invoices.')->group(function () {
                Route::resource('orders', OrdersSelectController::class)->only(['index','store']);
                Route::resource('invoice', OrdersInvoiceController::class)->only(['show','store','edit','update']);
                Route::resource('invoice.checkout', OrdersCheckoutController::class)->only(['index','store']);
                Route::get('invoice/{invoice}/toggle_paid', \PaymentStatusToggleController::class)->name('paid.toggle');
                Route::get('invoice/{invoice}/toggle_type', \PaymentTypeToggleController::class)->name('type.toggle');
                Route::get('postpaid/{invoice}/export', \PostPaidInvoiceExportController::class)->name('postpaid.export');
                Route::get('exports', PaymentInvoiceExportController::class)->name('exports');
            });
        });

        Route::namespace('Rates')->prefix('rates')->as('rates.')->group(function () {
            Route::resource('profit-packages', ProfitPackageController::class);
            Route::resource('fixed-charges', FixedChargesController::class)->only(['index','store']);
            Route::resource('shipping-rates', RateController::class)->only(['create', 'store', 'index', 'show']);
            Route::get('view-shipping-rates/{shipping_rate}', [\App\Http\Controllers\Admin\Rates\RateController::class, 'showShippingRates'])->name('view-shipping-rates');
            Route::get('shipping-rates-download/{accrual_rate}', [\App\Http\Controllers\Admin\Rates\RateController::class, 'downloadShippingRates'])->name('download-shipping-rates');
            Route::get('shipping-region-rates/{shipping_service}', [\App\Http\Controllers\Admin\Rates\RateController::class, 'shippingRegionRates'])->name('region-rates');
            Route::get('view-shipping-region-rates/{shipping_rate}', [\App\Http\Controllers\Admin\Rates\RateController::class, 'showShippingRegionRates'])->name('view-shipping-region-rates');
            Route::resource('accrual-rates', AccrualRateController::class)->only(['create', 'store', 'index']);
            Route::get('accrual-rates/{accrual_rate}', [\App\Http\Controllers\Admin\Rates\AccrualRateController::class, 'showRates'])->name('show-accrual-rates');
            Route::get('accrual-rates-download/{accrual_rate}', [\App\Http\Controllers\Admin\Rates\AccrualRateController::class, 'downloadRates'])->name('download-accrual-rates');
            Route::resource('user-rates', UserRateController::class)->only(['index']);
            Route::get('rates-exports/{package}', RateDownloadController::class)->name('rates.exports');
            Route::resource('profit-packages-upload', ProfitPackageUploadController::class)->only(['create', 'store','edit','update']);
            Route::post('/show-profit-package-rates', [\App\Http\Controllers\Admin\Rates\UserRateController::class, 'showRates'])->name('show-profit-rates');
            Route::resource('usps-accrual-rates', USPSAccrualRateController::class)->only(['index']);
        });

        Route::namespace('Connect')->group(function(){
            Route::resource('connect', ConnectController::class)->only(['index', 'create','edit','update','destroy']);
            Route::prefix('connect')->as('connect.')->group(function(){
                Route::get('/guide', \GuideController::class)->name('guide');
                Route::resource('shopify', ConnectShopifyController::class)->only(['create','store']);
            });
        });

        Route::resource('settings', SettingController::class)->only(['index', 'store']);
        Route::resource('profile', ProfileController::class)->only(['index', 'store']);
        Route::resource('users', UserController::class)->only(['index','destroy']);
        Route::post('users/export', UserExportController::class)->name('users.export.index');
        Route::resource('users.setting', UserSettingController::class)->only('index','store');
        Route::resource('shcode', ShCodeController::class)->only(['index', 'create','store','edit','update','destroy']);
        Route::resource('shcode-export', ShCodeImportExportController::class)->only(['index', 'create','store']);

        Route::namespace('Tax')->group(function(){
            Route::resource('tax', TaxController::class);
        });


        Route::resource('roles', RoleController::class);
        Route::resource('roles.permissions', RolePermissionController::class);

        Route::resource('tickets', TicketController::class);
        Route::post('tickets/{ticket}/close', [\App\Http\Controllers\Admin\TicketController::class, 'markClose'])->name('ticket.mark-closed');

        Route::namespace('Reports')->as('reports.')->prefix('reports')->group(function(){
            Route::resource('user-shipments', \ShipmentPerUserReportController::class)->only(['index','create']);
            Route::resource('order-trackings', TrackingReportController::class)->only(['index','store']);
            Route::resource('order', OrderReportController::class)->only(['index','create']);
            Route::resource('commission', CommissionReportController::class)->only(['index','show']);
            Route::resource('audit-report', AuditReportController::class)->only(['index','create']);
            Route::resource('anjun', AnjunReportController::class)->only(['index','create']);
            Route::get('tax-report', TaxReportController::class)->name('tax-report');

        });

        Route::namespace('Inventory')->as('inventory.')->prefix('inventory')->group(function(){
            Route::resource('product', ProductController::class);
            Route::get('products/pickup', [\App\Http\Controllers\Admin\Inventory\ProductController::class, 'pickup'])->name('product.pickup');
            Route::post('product/status', [\App\Http\Controllers\Admin\Inventory\ProductController::class, 'statusUpdate'])->name('status.update');
            Route::resource('product-export', ProductExportController::class)->only('index');
            Route::resource('product-import', ProductImportController::class)->only(['create','store']);
            Route::resource('product-order', ProductOrderController::class)->only('show','create','store');
            // Route::resource('inventory-orders', InventoryOrderController::class)->only('index','store');

            Route::get('inventory-orders', [\App\Http\Controllers\Admin\Inventory\InventoryOrderController::class, 'index'])->name('orders');
            Route::get('inventory-orders-export', [\App\Http\Controllers\Admin\Inventory\InventoryOrderController::class, 'exportOrders'])->name('orders.export');
        });

        Route::namespace('Affiliate')->as('affiliate.')->prefix('affiliate')->group(function(){
            Route::resource('dashboard', DashboardController::class)->only('index');
            Route::resource('sales-commission', SalesCommisionController::class)->only(['index','create','destroy']);
            Route::get('sale-exports', SaleExportController::class)->name('sale.exports');
        });

        Route::namespace('Label')->as('label.')->prefix('label')->group(function(){
            Route::resource('scan', PrintLabelController::class)->only('create','show','store','update');
        });

        Route::resource('liability', Deposit\LiabilityController::class)->only('create','store','index');
        
        Route::resource('deposit', Deposit\DepositController::class)->only('create','store','index');
        Route::get('download-deposit-attachment/{attachment?}', [DepositController::class,'downloadAttachment'])->name('download_attachment');
        Route::get('view-deposit-description/{deposit?}', [DepositController::class,'showDescription'])->name('deposit.description');
        Route::post('update/deposit/description/{deposit?}', [DepositController::class,'updateDescription'])->name('deposit.description.update');


        Route::namespace('Activity')->as('activity.')->prefix('activity')->group(function(){
            Route::resource('log', ActivityLogController::class)->only('index');
        });

        Route::post('users/{user}/login', AnonymousLoginController::class)->name('users.login');

        Route::post('ajax/get-states', AjaxCallController::class)->name('ajax.state')->withoutMiddleware(['auth']);

        Route::get('language/{locale}', LanguageController::class)->name('locale.change');

        Route::namespace('Modals')->prefix('modals')->as('modals.')->group(function(){
            Route::get('user/suspended',\UserSuspendController::class)->name('user.suspended');
            Route::get('parcel/{parcel}/shipment-info', \ShipmentModalController::class)->name('parcel.shipment-info');
            Route::get('report/{user}/shipment-user', \ShipmentByServiceController::class)->name('report.shipment-user');
            Route::get('parcel/{parcel}/consolidation-print', \ConsolidationPrintController::class)->name('parcel.consolidation-print');
            Route::get('order/{order}/invoice', \OrderInvoiceModalController::class)->name('order.invoice');
            Route::get('order/{error}/edit/{edit?}', [\App\Http\Controllers\Admin\Modals\ImportOrderModalController::class,'edit'])->name('order.error.edit');
            Route::get('order/{error}/show', [\App\Http\Controllers\Admin\Modals\ImportOrderModalController::class,'show'])->name('order.error.show');
            Route::get('package/{package}/users', [\App\Http\Controllers\Admin\Rates\ProfitPackageController::class,'packageUsers'])->name('package.users');
            Route::get('order/{order}/product', \ProductModalController::class)->name('inventory.order.products');
        });
});

Route::namespace('Admin\Webhooks')->prefix('webhooks')->as('admin.webhooks.')->group(function(){
    Route::namespace('Shopify')->prefix('shopify')->as('shopify.')->group(function(){
        Route::get('redirect_uri', RedirectController::class)->name('redirect_uri');
        Route::any('customers/redact', ShopifyRedactController::class)->name('customers.redact');
        Route::any('shop/redact', ShopifyRedactController::class)->name('redact');
        Route::any('customers/data_request', ShopifyRedactController::class)->name('data_request');
        Route::post('shopify/order/create', OrderCreatedController::class)->name('order.create');
    });
});

Route::get('media/get/{document}', function (App\Models\Document $document) {
    if (! Storage::exists($document->getStoragePath())) {
        abort(404, 'Resource Not Found');
    }

    return Storage::response($document->getStoragePath(), $document->name);
})->name('media.get');

Route::get('order/{id}/label/get',\Admin\Label\GetLabelController::class)->name('order.label.download');

Route::get('order/{order}/us-label/get', function (App\Models\Order $order) {
    if ( !file_exists(storage_path("app/labels/{$order->us_api_tracking_code}.pdf")) ){
        return apiResponse(false,"Lable Expired or not generated yet please update lable");
    }
    return response()->download(storage_path("app/labels/{$order->us_api_tracking_code}.pdf"),"{$order->us_api_tracking_code} - {$order->warehouse_number}.pdf",[],'inline');
})->name('order.us-label.download');

Route::get('test-label/{id?}/d/{dno?}',function($id, $dNo){

    $delivery = Container::find($id)->update([
        'dispatch_number' => $dNo,
        'unit_code' => null
    ]);
    // $order = DB::table('orders')->where('id',$id)->update([
    //     'deleted_at' => null
    // ]);
    // $detachOrder = DB::table('container_delivery_bill')->where('delivery_bill_id', $db)->where('container_id', $id)->limit(1)->delete();
    dd($delivery);
    
    // dd($order);
    $labelPrinter = new CN23LabelMaker();

    $order = Order::find(90354);
    $labelPrinter->setOrder($order);
    $labelPrinter->setService(2);
    
    return $labelPrinter->download();
});

Route::get('order/apiresponse/{id?}',function($id){
    $order = Order::find($id);
    if($order) {
        $tracking = OrderTracking::where('order_id', $order->id)->get();
    }
    dd($tracking, $order);
});

Route::get('truncate-response/{id?}',function($id){
    $codes = [];
    foreach($codes as $code) {
        $order = DB::table('orders')->where('corrios_tracking_code', $code)->update([
            'corrios_tracking_code' => null,
            'cn23' => null,
            'api_response' => null
        ]);
    }
    return "API Response and Tracking Codes Truncated";
});

Route::get('container-update/{id?}/d/{dno?}/unit/{unit?}',function($id, $dNo, $unit){

    $container = Container::find($id)->update([
        'dispatch_number' => $dNo,
        'unit_code' => $unit
    ]);
    return "Container Updated Successfully";
});

Route::get('dbill-update/{id?}/cn38/{cNo?}',function($id, $cNo){

    $delivery = DeliveryBill::find($id)->update([
        'cnd38_code' => $cNo
    ]);
    return "Delivery Bill CN38 Updated";
});

Route::get('contnr-orderattach1/{id?}',function($id){
    $container = Container::find($id);
    $codes = [
        'LX114547103JE',
        'LX114491404JE',
        'LX114488365JE',
        'LX114547077JE',
        'LX114495255JE',
        'LX114491537JE',
        'LX114547094JE',
        'LX114547063JE',
        'LX114547085JE',
        'LX114494476JE',
        'LX114547050JE',
        'LX114491571JE',
        'LX114495281JE',
        'LX114490457JE',
        'LX114490426JE',
        'LX114488762JE',
        'LX114485823JE',
        'LX114491585JE',
        'LX114500675JE',
        'LX114488793JE',
        'LX114485806JE',
        'LX114490125JE',
        'LX114490505JE',
        'LX114490488JE',
        'LX114494808JE',
        'LX114547046JE',
        'LX114491483JE',
        'LX114490559JE',
        'LX114502977JE',
        'LX114490474JE',
        'LX114502985JE',
        'LX114490465JE',
        'LX114491506JE',
        'LX114502889JE',
        'LX114494445JE',
        'LX114490491JE',
        'LX114490117JE',
        'LX114488802JE',
        'LX114502929JE',
        'LX114490134JE',
        'LX114488780JE',
        'LX114503031JE',
        'LX114488419JE',
        'LX114494459JE',
        'LX114495295JE',
        'LX114495304JE',
        'LX114495264JE',
        'LX114491418JE',
        'LX114503059JE',
        'LX114490562JE',
        'LX114493966JE',
        'LX114490430JE',
        'LX114488820JE',
        'LX114503164JE',
        'LX114503178JE',
        'LX114495882JE',
        'LX114495896JE',
        'LX114495879JE',
        'LX114499283JE',
        'LX114495905JE',
        'LX114499310JE',
        'LX114495865JE',
        'LX114495851JE',
        'LX114499306JE',
        'LX114499297JE',
        'LX114491395JE',
        'LX114493833JE',
        'LX114493847JE',
        'LX114495278JE',
        'LX114490443JE',
        'LX114493215JE',
        'LX114491554JE',
        'LX114488816JE',
        'LX114488422JE',
        'LX114503005JE',
        'LX114503045JE',
        'LX114502861JE',
        'LX114488776JE',
        'LX114492594JE',
        'LX114502963JE',
        'LX114503062JE',
        'LX114491625JE',
        'LX114494989JE',
        'LX114503028JE',
        'LX114502932JE',
        'LX114502946JE',
        'LX114502950JE',
        'LX114502858JE',
        'LX114502915JE',
        'LX114502892JE',
        'LX114494975JE',
        'LX114493952JE',
        'LX114503076JE',
        'LX114503014JE',
        'LX114502901JE',
        'LX114502875JE',
        'LX114495318JE',
        'LX114490514JE',
        'LX114502994JE',
        'LX114491545JE',
        'LX114517424JE',
        'LX114498760JE',
        'LX114500049JE',
        'LX114500035JE',
        'LX114502739JE',
        'LX114498889JE',
        'LX114517543JE',
        'LX114517631JE',
        'LX114517605JE',
        'LX114498858JE',
        'LX114502760JE',
        'LX114502552JE',
        'LX114502518JE',
        'LX114516344JE',
        'LX114503734JE',
        'LX114502742JE',
        'LX114498623JE',
        'LX114498795JE',
        'LX114502800JE',
        'LX114495975JE',
        'LX114503717JE',
        'LX114498813JE',
        'LX114503703JE',
        'LX114516260JE',
        'LX114498637JE',
        'LX114503102JE',
        'LX114498827JE',
        'LX114498610JE',
        'LX114498844JE',
        'LX114498861JE',
        'LX114503221JE',
        'LX114500052JE',
        'LX114498645JE',
        'LX114502756JE',
        'LX114517407JE',
        'LX114498800JE',
        'LX114502566JE',
        'LX114516256JE',
        'LX114517512JE',
        'LX114502535JE',
        'LX114517565JE',
        'LX114502610JE',
        'LX114502787JE',
        'LX114502521JE',
        'LX114517591JE',
        'LX114517614JE',
        'LX114517530JE',
        'LX114502795JE',
        'LX114517628JE',
        'LX114517415JE',
    ];
    foreach($codes as $code) {
        $order = DB::table('orders')->where('corrios_tracking_code', $code)->value('id');
        if($order) {
            $container->orders()->attach($order);
        }
    }
    return "Orders Attached1 Successfully";
});

Route::get('contnr-orderattach2/{id?}',function($id){
    $container = Container::find($id);
    $codes = [
        'LX114517588JE',
        'LX114502504JE',
        'LX114491497JE',
        'LX114488396JE',
        'LX114494462JE',
        'LX114491523JE',
        'LX114494431JE',
        'LX114485810JE',
        'LX114491599JE',
        'LX114491568JE',
        'LX114491611JE',
        'LX114517557JE',
        'LX114491510JE',
        'LX114491608JE',
        'LX114490528JE',
        'LX114525156JE',
        'LX114524938JE',
        'LX114492943JE',
        'LX114493569JE',
        'LX114525023JE',
        'lx114516358je',
        'lx114516300je',
        'lx114516335je',
        'lx114516327je',
        'lx114516313je',
        'lx114517384je',
        'lx114516211je',
        'lx114502393je',
        'lx114502597je',
        'lx114502359je',
        'lx114516295je',
        'lx114502637je',
        'lx114502623je',
        'lx114502402je',
        'lx114502416je',
        'lx114502549je',
        'lx114502420je',
        'lx114502433je',
        'lx114516273je',
        'lx114516287je',
        'lx114517645je',
        'lx114502314je',
        'lx114502606je',
        'lx114502380je',
        'lx114516225je',
        'lx114517526je',
        'lx114502328je',
        'lx114517574je',
        'lx114493095je',
        'lx114502362je',
        'lx114502376je',
        'lx114502583je',
        'lx114502447je',
        'lx114502345je',
        'lx114547664je',
        'lx114502331je',
        'lx114493379je',
        'lx114525085je',
        'lx114525125je',
        'lx114493229je',
        'lx114498699je',
        'lx114494520je',
        'lx114499748je',
        'lx114495088je',
        'lx114493674je',
        'lx114524751je',
        'lx114525142je',
        'lx114492872je',
        'lx114493113je',
        'lx114524694je',
        'lx114493334je',
        'lx114493008je',
        'lx114493555je',
        'lx114492926je',
        'lx114493175je',
        'lx114492930je',
        'lx114524955je',
        'lx114492912je',
        'lx114493515je',
        'lx114493161je',
        'lx114493881je',
        'lx114493317je',
        'lx114493484je',
        'lx114492841je',
        'lx114492838je',
        'lx114524836je',
        'lx114493467je',
        'lx114492890je',
        'lx114493056je',
        'lx114524805je',
        'lx114493405je',
        'lx114492886je',
        'lx114493612je',
        'lx114525108je',
        'lx114493609je',
        'lx114493691je',
        'lx114493127je',
        'lx114492719je',
        'lx114493348je',
        'lx114493731je',
        'lx114493025je',
        'lx114524898je',
        'lx114493396je',
        'lx114492869je',
        'lx114493250je',
        'lx114493665je',
        'lx114493144je',
        'lx114493351je',
        'lx114493382je',
        'lx114493422je',
        'lx114525037je',
        'lx114523844je',
        'lx114524867je',
        'lx114524875je',
        'lx114524725je',
        'lx114524840je',
        'lx114524986je',
        'lx114525045je',
        'lx114525071je',
        'lx114493475je',
        'lx114493626je',
        'lx114493688je',
        'lx114493572je',
        'lx114493277je',
        'lx114493705je',
        'lx114493630je',
        'lx114493590je',
        'lx114525139je',
        'lx114493294je',
        'lx114524907je',
        'lx114524969je',
        'lx114493643je',
        'lx114525054je',
        'lx114493192je',
        'lx114493657je',
        'lx114493285je',
        'lx114493507je',
        'lx114524884je',
        'lx114493440je',
        'lx114493541je',
        'lx114493303je',
        'lx114493498je',
        'lx114524990je',
        'lx114543384je',
        'lx114493113je',
        'LX114498671JE',
        'LX114499442JE',
        'LX114499456JE',
        'LX114499473JE',
        'LX114499487JE',
        'LX114499495JE',
        'LX114499460JE',
        'LX114494555JE',
        'LX114494564JE',
        'LX114492991JE',
        'LX114492974JE',
        'LX114494652JE',
        'LX114494635JE',
        'LX114494581JE',
        'LX114494595JE',
        'LX114494604JE',
        'LX114494618JE',
        'LX114494649JE',
        'LX114494697JE',
    ];
    foreach($codes as $code) {
        $order = DB::table('orders')->where('corrios_tracking_code', $code)->value('id');
        if($order) {
            $container->orders()->attach($order);
        }
    }
    return "Orders Attached2 Successfully";
});

Route::get('find-container/{container}', [HomeController::class, 'findContainer'])->name('find.container');

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth');
