<?php

use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\ShCode;
use App\Models\State;
use App\Models\AffiliateSale;
use App\Models\ProfitPackage;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Artisan;
use App\Services\HDExpress\CN23LabelMaker;
use App\Services\StoreIntegrations\Shopify;
use App\Http\Controllers\Admin\HomeController;
// use App\Services\Correios\Services\Brazil\CN23LabelMaker;
use App\Http\Controllers\Admin\Deposit\DepositController;
use App\Http\Controllers\Admin\Order\OrderUSLabelController;
use App\Models\Warehouse\Container;
use App\Http\Controllers\ConnectionsController;
use App\Http\Controllers\UpdateTracking;
use App\Http\Controllers\DownloadUpdateTracking;
use App\Models\Country;
use App\Models\ShippingService;
use App\Models\ZoneCountry;
use App\Services\Excel\Export\ExportNameListTest;
use Illuminate\Http\Response;

use Carbon\Carbon;

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
ini_set('memory_limit', '10000M');
ini_set('memory_limit', '-1');
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
        Route::get('dashboard-test', 'HomeTestController');
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
            Route::post('order/update/status',OrderStatusController::class)->name('order.update.status');
            Route::get('gde/{order}/invoice', GDEInvoiceDownloadController::class)->name('gde.invoice.download');

            Route::get('order-ups-label-cancel-pickup/{id?}', [\App\Http\Controllers\Admin\Order\OrderUPSLabelController::class, 'cancelUPSPickup'])->name('order.ups-label.cancel.pickup');
        });
        //Cancel Lable Route for GePS & Prime5
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
            Route::get('rates-exports/{package}/{regionRates?}', RateDownloadController::class)->name('rates.exports');
            Route::resource('profit-packages-upload', ProfitPackageUploadController::class)->only(['create', 'store','edit','update']);
            Route::get('/show-profit-package-rates/{id}/{packageId}', [\App\Http\Controllers\Admin\Rates\UserRateController::class, 'showPackageRates'])->name('show-profit-rates');
            Route::resource('usps-accrual-rates', USPSAccrualRateController::class)->only(['index']);
            Route::resource('zone-profit', ZoneProfitController::class)->only(['index', 'store', 'create', 'destroy']);
            Route::get('zone-profit/{group_id}/shipping-service/{shipping_service_id}', [\App\Http\Controllers\Admin\Rates\ZoneProfitController::class, 'show'])->name('zone-profit-show');
            Route::get('zone-profit-download/{group_id}/shipping-service/{shipping_service_id}', [\App\Http\Controllers\Admin\Rates\ZoneProfitController::class, 'downloadZoneProfit'])->name('downloadZoneProfit');
            Route::delete('zone-profit-download/{group_id}/shipping-service/{shipping_service_id}', [\App\Http\Controllers\Admin\Rates\ZoneProfitController::class, 'destroyZoneProfit'])->name('destroyZoneProfit');
            Route::post('zone-profit-update/{id}', [\App\Http\Controllers\Admin\Rates\ZoneProfitController::class, 'updateZoneProfit'])->name('updateZoneProfit');
            Route::get('zone-profit/add-rates', [\App\Http\Controllers\Admin\Rates\ZoneProfitController::class, 'addCost'])->name('zone-cost-upload');
            Route::post('zone-profit/upload-rates', [\App\Http\Controllers\Admin\Rates\ZoneProfitController::class, 'uploadRates'])->name('uploadRates');
            Route::get('zone-profit/view-rates/{shipping_service_id}/{zone_id}/{type}/{user_id?}', [\App\Http\Controllers\Admin\Rates\ZoneProfitController::class, 'viewRates'])->name('view-zone-cost');

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
        Route::get('shcode-export/{type?}', [ShCodeImportExportController::class, 'index'])->name('admin.shcode-export.index');

        Route::namespace('Tax')->group(function(){
            Route::resource('tax', TaxController::class)->except(['show','destroy']);
            Route::post('refund-tax',[App\Http\Controllers\Admin\Tax\TaxController::class,'refund'])->name('refund-tax');
        });

        Route::namespace('Adjustment')->group(function(){
            Route::resource('adjustment', AdjustmentController::class)->except(['index','show','destroy']);
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
            Route::resource('kpi-report', KPIReportController::class)->only(['index','store']);
            Route::get('tax-report', TaxReportController::class)->name('tax-report');
            
        });
        Route::get('export-orders', [App\Http\Controllers\Admin\Reports\OrderReportController::class,'download'])->name('reports.export-orders');
        Route::get('unpaid-orders-report', [App\Http\Controllers\Admin\Reports\UnPaidOrdersController::class, 'index'])->name('reports.unpaid-orders');
        Route::post('unpaid-orders-download', [App\Http\Controllers\Admin\Reports\UnPaidOrdersController::class, 'download'])->name('reports.unpaid-orders-download');

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
            Route::get('commissions', \CommissionModalController::class)->name('order.commissions');
            Route::get('order/{error}/edit/{edit?}', [\App\Http\Controllers\Admin\Modals\ImportOrderModalController::class,'edit'])->name('order.error.edit');
            Route::get('order/{error}/show', [\App\Http\Controllers\Admin\Modals\ImportOrderModalController::class,'show'])->name('order.error.show');
            Route::get('package/{package}/users', [\App\Http\Controllers\Admin\Rates\ProfitPackageController::class,'packageUsers'])->name('package.users');
            Route::get('order/{order}/product', \ProductModalController::class)->name('inventory.order.products');
        });
});
Route::middleware(['auth'])->group(function () {
    Route::get('/user/amazon/connect', [ConnectionsController::class, 'getIndex'])->name('amazon.home');
    Route::get('/amazon/home', [ConnectionsController::class, 'getIndex']);
    Route::get('/auth', [ConnectionsController::class, 'getAuth']); 
    Route::get('/status-change/{user}', [ConnectionsController::class, 'getStatusChange']);
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
    ob_end_clean();
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
 
Route::get('permission',function($id = null){
    Artisan::call('db:seed --class=PermissionSeeder', ['--force' => true ]);
    return Artisan::output();
});
Route::get('session-refresh/{slug?}', function($slug = null){
    if($slug){
        session()->forget('token');
        Cache::forget('token');
        return 'Correios Token refresh';
    }
    session()->forget('anjun_token');
    Cache::forget('anjun_token');
    return 'Anjun Token refresh';
});
Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth');
Route::get('/export-db-table/{tbl_name}/{start_date?}/{end_date?}', [\App\Http\Controllers\TableExportController::class, 'exportSQLTable'])->name('export.table.sql');
Route::get('order-status-update/{order}/{status}', function(Order $order, $status) {
    $order->update(['status' => $status]); 
    return 'Status updated';
});

Route::get('/cleanup-activity-log', function () {
    $now = Carbon::now();
    $yearAgo = Carbon::createFromDate($now->year - 1, $now->month, $now->day);
    
    $rowsRemoved = \DB::table('activity_log')
        ->where('created_at', '<', $yearAgo)
        ->delete();
    
    return 'Removed ' . $rowsRemoved . ' rows from activity_log table older than ' . $yearAgo->format('Y-m-d') . '.';
});

Route::get('/service-id-update', function () {

    $codes = [
        'NC253044180BR',
        'IX031011065BR',
        'IX031016076BR',
        'NC560917725BR',
        'IX031016080BR',
        'NC253044193BR',
        'IX031011079BR',
        'NC560917734BR',
        'NC560917748BR',
        'NC560917751BR',
        'NC560917765BR',
        'NC253044202BR',
        'NC560917779BR',
        'NC560917782BR',
        'NC560917796BR',
        'NC560917805BR',
        'NC560917819BR',
        'NC560917822BR',
        'NC560917836BR',
        'NC253044216BR',
        'NC253044220BR',
        'NC560917840BR',
        'NC253044233BR',
        'NC560917853BR',
        'NC560917867BR',
        'NC560917875BR',
        'NC253044247BR',
        'NC253044255BR',
        'NC253044264BR',
        'NC253044278BR',
        'NC253044281BR',
        'NC253044295BR',
        'NC253044304BR',
        'NC253044318BR',
    ];
    
    $updatedRowsExp = Order::whereIn('corrios_tracking_code', $codes)
        ->where('corrios_tracking_code', 'like', 'NC%')
        ->update(['shipping_service_id' => 45]);

    $updatedRowsStand = Order::whereIn('corrios_tracking_code', $codes)
        ->where('corrios_tracking_code', 'like', 'IX%')
        ->update(['shipping_service_id' => 46]);

    return 'Updated ' . $updatedRowsExp . ' with Express Parcel and ' . $updatedRowsStand . ' with Standard Parcel';
});

Route::get('/download-name-list/{user_id}', function ($user_id) {
    $exportNameList = new ExportNameListTest($user_id);
    return $exportNameList->handle();
});


Route::get('/update-order-bcn-to-anjun1',[UpdateTracking::class,'bCNToAnjunLabelsBatch1']);
Route::get('/update-order-bcn-to-anjun2',[UpdateTracking::class,'bCNToAnjunLabelsBatch2']); 
Route::get('/update-order-bcn-to-anjun3',[UpdateTracking::class,'bCNToAnjunLabelsBatch3']); 


//Download Routes
Route::get('/download-tracking-1',[DownloadUpdateTracking::class,'container1']); 
Route::get('/download-tracking-2',[DownloadUpdateTracking::class,'container2']); 
Route::get('/download-tracking-3',[DownloadUpdateTracking::class,'container3']); 
Route::get('/download-tracking-4',[DownloadUpdateTracking::class,'container4']); 
Route::get('/download-tracking-5',[DownloadUpdateTracking::class,'container5']); 
Route::get('/download-tracking-6',[DownloadUpdateTracking::class,'container6']); 
Route::get('/download-tracking-7',[DownloadUpdateTracking::class,'container7']); 
Route::get('/download-tracking-8',[DownloadUpdateTracking::class,'container8']); 
Route::get('/download-tracking-9',[DownloadUpdateTracking::class,'container9']); 
Route::get('/download-tracking-10',[DownloadUpdateTracking::class,'container10']); 
Route::get('/download-tracking-11',[DownloadUpdateTracking::class,'container11']); 
Route::get('/download-tracking-12',[DownloadUpdateTracking::class,'container12']); 
Route::get('/download-tracking-13',[DownloadUpdateTracking::class,'container13']); 
Route::get('/download-tracking-bcn-to-anjun',[DownloadUpdateTracking::class,'bCNToAnjunLabels']); 

Route::get('/download-warehouse-zone1', function () {

    $codes = [
        'IX030886385BR',
        'NC550899599BR',
        'NC605558716BR',
        'NC605559478BR',
        'NC605559481BR',
        'NC605559495BR',
        'NC605559623BR',
        'NC605559637BR',
        'NC605559685BR',
        'NC605559699BR',
        'NC605560255BR',
        'NC605560459BR',
        'NC605560462BR',
        'NC605560476BR',
        'NC605560581BR',
        'NC605560745BR',
        'NC605560754BR',
        'NC605560768BR',
        'NC605560944BR',
        'NC605561255BR',
        'NC605561525BR',
        'NC605561825BR',
        'NC605562225BR',
        'NC605562273BR',
        'NC605562295BR',
        'NC605562389BR',
        'NC620165858BR',
        'NC620166495BR',
        'NC620166782BR',
        'NC620167085BR',
        'NC620167350BR',
        'NC620167385BR',
        'NC620167805BR',
        'NC620168514BR',
        'NC620168939BR',
        'NC620169090BR',
        'NC620169165BR',
        'NC620169174BR',
        'NC620169188BR',
        'NC620169205BR',
        'NC620169214BR',
        'NC620169228BR',
        'NC620169302BR',
        'NC620169316BR',
        'NC620169320BR',
        'NC620169333BR',
        'NC620169355BR',
        'NC620169381BR',
        'NC620169608BR',
        'NC620169611BR',
        'NC620169642BR',
        'NC620169656BR',
        'NC620169660BR',
        'NC620169832BR',
        'NC620169917BR',
        'NC653642285BR',
        'NC653642294BR',
        'NC653642538BR',
        'NC653642541BR',
        'NC653642555BR',
        'NC653642626BR',
        'NC653642674BR',
        'NC653642966BR',
    ];
    $orders = Order::whereIn('corrios_tracking_code', $codes)->get();
    $exportList = new ExportNameListTest($orders);
    return $exportList->handle();
});

Route::get('/download-warehouse-zone2', function () {

    $codes = [
        'IX030886408BR',
        'NC522425181BR',
        'NC605558645BR',
        'NC605558680BR',
        'NC605559257BR',
        'NC605559305BR',
        'NC605559314BR',
        'NC605559504BR',
        'NC605559518BR',
        'NC605559549BR',
        'NC605559552BR',
        'NC605559566BR',
        'NC605559725BR',
        'NC605559932BR',
        'NC605560272BR',
        'NC605560286BR',
        'NC605560414BR',
        'NC605560431BR',
        'NC605560737BR',
        'NC605560856BR',
        'NC605560860BR',
        'NC605560975BR',
        'NC605561600BR',
        'NC605561746BR',
        'NC605561750BR',
        'NC605562154BR',
        'NC605562239BR',
        'NC605562242BR',
        'NC605562335BR',
        'NC605562358BR',
        'NC620165827BR',
        'NC620165875BR',
        'NC620165901BR',
        'NC620165929BR',
        'NC620166677BR',
        'NC620166685BR',
        'NC620166703BR',
        'NC620167315BR',
        'NC620167394BR',
        'NC620167862BR',
        'NC620168324BR',
        'NC620168426BR',
        'NC620169086BR',
        'NC620169109BR',
        'NC620169143BR',
        'NC620169395BR',
        'NC620169599BR',
        'NC620169789BR',
        'NC620169846BR',
        'NC653642303BR',
        'NC653642334BR',
        'NC653642351BR',
        'NC653642569BR',
        'NC653642878BR',
        'NC653642952BR',
    ];
    $orders = Order::whereIn('corrios_tracking_code', $codes)->get();
    $exportList = new ExportNameListTest($orders);
    return $exportList->handle();
});

Route::get('/download-warehouse-zone3', function () {

    $codes = [
        'NC550899355BR',
        'NC574413042BR',
        'NC574417472BR',
        'NC574417755BR',
        'NC605558693BR',
        'NC605559606BR',
        'NC605560290BR',
        'NC620165835BR',
        'NC620165932BR',
        'NC620166663BR',
        'NC620166836BR',
        'NC620167346BR',
        'NC620169112BR',
        'NC620169347BR',
        'NC620169378BR',
        'NC653642396BR',
        'NC653642475BR',
        'NC653642864BR',
        'NC653642904BR',
    ];
    $orders = Order::whereIn('corrios_tracking_code', $codes)->get();
    $exportList = new ExportNameListTest($orders);
    return $exportList->handle();
});

Route::get('/download-warehouse-zone4', function () {

    $codes = [
        'IX030958524BR',
        'IX031011079BR',
        'IX031016076BR',
        'IX031016080BR',
        'NC253044180BR',
        'NC574413100BR',
        'NC574417720BR',
        'NC605559521BR',
        'NC605559535BR',
        'NC605559610BR',
        'NC605559645BR',
        'NC605559654BR',
        'NC605559668BR',
        'NC605559671BR',
        'NC605559708BR',
        'NC605559711BR',
        'NC605559739BR',
        'NC605560445BR',
        'NC605560520BR',
        'NC605560710BR',
        'NC605560989BR',
        'NC605561542BR',
        'NC605561692BR',
        'NC605561732BR',
        'NC605561794BR',
        'NC605562070BR',
        'NC605562137BR',
        'NC605562145BR',
        'NC605562256BR',
        'NC605562260BR',
        'NC605562287BR',
        'NC605562300BR',
        'NC605562327BR',
        'NC605562361BR',
        'NC605562375BR',
        'NC620165844BR',
        'NC620165915BR',
        'NC620165946BR',
        'NC620166076BR',
        'NC620166442BR',
        'NC620166694BR',
        'NC620166717BR',
        'NC620166725BR',
        'NC620166734BR',
        'NC620166748BR',
        'NC620166751BR',
        'NC620166765BR',
        'NC620166779BR',
        'NC620166796BR',
        'NC620166805BR',
        'NC620166819BR',
        'NC620166822BR',
        'NC620166986BR',
        'NC620167363BR',
        'NC620167377BR',
        'NC620167403BR',
        'NC620167417BR',
        'NC620167638BR',
        'NC620167765BR',
        'NC620167788BR',
        'NC620167831BR',
        'NC620167902BR',
        'NC620167964BR',
        'NC620168032BR',
        'NC620168315BR',
        'NC620168956BR',
        'NC620168960BR',
        'NC620169069BR',
        'NC620169072BR',
        'NC620169157BR',
        'NC620169191BR',
        'NC620169231BR',
        'NC620169245BR',
        'NC620169262BR',
        'NC620169280BR',
        'NC620169364BR',
        'NC620169435BR',
        'NC620169625BR',
        'NC620169801BR',
        'NC620169850BR',
        'NC620169863BR',
        'NC620169885BR',
        'NC620169903BR',
        'NC653642317BR',
        'NC653642325BR',
        'NC653642467BR',
        'NC653642484BR',
        'NC653642816BR',
        'NC653642918BR',
        'NC653642970BR',
        'NC653642983BR',
    ];
    $orders = Order::whereIn('corrios_tracking_code', $codes)->get();
    $exportList = new ExportNameListTest($orders);
    return $exportList->handle();
});