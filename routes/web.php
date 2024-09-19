<?php

use Carbon\Carbon;
use App\Models\Order;
use App\Models\State;
use App\Models\ShCode;
use App\Models\Country;
use App\Models\ZoneCountry;
use Illuminate\Http\Request;
use App\Models\AffiliateSale;
use App\Models\ProfitPackage;
use Illuminate\Http\Response;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
// use App\Services\Correios\Services\Brazil\CN23LabelMaker;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\UpdateTracking;
use App\Services\HDExpress\CN23LabelMaker;
use App\Services\StoreIntegrations\Shopify;
use App\Http\Controllers\Admin\HomeController;
use App\Services\Excel\Export\TempOrderExport;
use App\Http\Controllers\ConnectionsController;
use App\Http\Controllers\DownloadUpdateTracking;
use App\Services\Excel\Export\OrderUpdateExport;

use App\Services\Excel\Export\ExportNameListTest;
use App\Http\Controllers\Admin\Deposit\DepositController;
use App\Http\Controllers\Admin\Order\OrderUSLabelController;
use App\Repositories\AnjunLabelRepository;

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
            Route::get('rates-exports/{package}/{service?}/{regionRates?}', RateDownloadController::class)->name('rates.exports');
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
    Route::resource('us-calculator', USCalculatorController::class)->only(['index', 'store']);
    Route::resource('calculator', CalculatorController::class)->only(['index', 'store']);
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
    Artisan::call('db:seed', ['--class'=>"PermissionSeeder",'--force' => true ]);
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

Route::get('/download-name-list/{user_id}', function ($user_id) {
    $exportNameList = new ExportNameListTest($user_id);
    return $exportNameList->handle();
});

Route::get('/update-order-tracking',[UpdateTracking::class,'update']); 


Route::get('/download-updated-tracking',[DownloadUpdateTracking::class,'download']);

Route::get('/fail-jobs', function () {
    $failedJobs = DB::table('failed_jobs')->get();
    foreach ($failedJobs as $job) {
        dump($job);
    }
    dd([
        'status' => 'success',
        'message' => 'Failed jobs dumped successfully.'
    ]);
});
Route::get('/delete-fail-jobs', function () {
    return DB::table('failed_jobs')->delete(); 
});
Route::get('/ispaid-order', function () {

    $codes = [
        'HD2433905516BR',
    ]; 
    Order::whereIn('warehouse_number', $codes)
        ->update(['is_paid'=>true]);

    return 'Status Updated';
});
Route::get('/orderbyid/{id}', function ($id, Request $request) {
    $query = Order::where('shipping_service_id', $id);

    if ($request->has('from') && $request->has('to')) {
        $from = $request->query('from') . ' 00:00:00';
        $to = $request->query('to') . ' 23:59:59';
        $query->whereBetween('order_date', [$from, $to]);
    }

    $orders = $query->get();
    \Log::info([$orders]);

    $ordersdownload = new TempOrderExport($orders);
    $filePath = $ordersdownload->handle();

    return response()->download($filePath)->deleteFileAfterSend(true);
});
Route::get('/warehouse-detail/{warehouse}/{field}', function ($warehouse,$field) {
    $order = (Order::where('warehouse_number', $warehouse)->first());  
    \Log::info(
        $order->toArray()
    );
    dump($order->update([$field=>null]));  
    dd($order);
});
Route::get('/anjun-china-label/{warehouse}', function ($warehouse,Request $request) {
    $order = (Order::where('warehouse_number', $warehouse)->first());  
    if($order){ 
    $order->shipping_service_id = 43;
    $order->save();
    $order->fresh();
        $anjun= new AnjunLabelRepository($order, $request, true);
        $anjunResponse = $anjun->run(); 
        dump([ "anjun run response front"=>$anjunResponse]);
        dump([ "anjun run response error"=>$anjun->getError()]);  
        dd('done');
    }
    dd('order not found');
});
Route::get('/remove-container-orders', function (Request $request) {
    $codes = [
        'ND067762066BR',
        'ND072972270BR',
        'IX031018633BR',
    ];
    $orders = Order::whereIn('corrios_tracking_code', $codes)->get();

    foreach ($orders as $order) {
        foreach ($order->containers as $container) {
            $container->orders()->detach($order->id);
        }
    }
    return "Orders Detached Successfully";
});

Route::get('/download-return-orders', function (Request $request) {
    set_time_limit(300);
    $codes = [
        "ND047213604BR",
        "IX031018298BR",
        "ND039576567BR",
        "IX031018324BR",
        "ND039576451BR",
        "ND039576553BR",
        "ND047215300BR",
        "ND014462135BR",
        "ND039573999BR",
        "ND047212759BR",
        "ND047212762BR",
        "ND039574019BR",
        "ND039574022BR",
        "ND047212776BR",
        "ND039574053BR",
        "ND047212780BR",
        "ND047212793BR",
        "ND039574084BR",
        "ND039574098BR",
        "ND047212816BR",
        "ND039574124BR",
        "ND039574155BR",
        "ND039574169BR",
        "ND047212833BR",
        "ND039574186BR",
        "ND047213581BR",
        "ND039574946BR",
        "ND047215344BR",
        "ND047215809BR",
        "ND072972632BR",
        "ND067762534BR",
        "ND072972646BR",
        "ND072973831BR",
        "ND072973859BR",
        "ND072973876BR",
        "ND067763645BR",
        "ND072973893BR",
        "ND067763676BR",
        "ND067763693BR",
        "ND067763720BR",
        "ND072973955BR",
        "ND072973964BR",
        "ND067763733BR",
        "ND067763755BR",
        "ND072974015BR",
        "ND067764518BR",
        "ND072975072BR",
        "ND072975090BR",
        "ND072973978BR",
        "ND072974236BR",
        "ND072974253BR",
        "ND067764138BR",
        "ND067764504BR",
        "ND072975761BR",
        "ND087691695BR",
        "ND072975979BR",
        "ND087691766BR",
        "ND072976016BR",
        "ND072976081BR",
        "ND072976387BR",
        "ND087692735BR",
        "ND096746105BR",
        "ND017077202BR",
        "ND017077281BR",
        "ND014466716BR",
        "ND017077349BR",
        "ND014466821BR",
        "ND017077383BR",
        "ND017078313BR",
        "ND018638941BR",
        "ND017079659BR",
        "ND018640052BR",
        "ND018640070BR",
        "ND014466662BR",
        "ND017077255BR",
        "ND017077295BR",
        "ND014466733BR",
        "ND014466852BR",
        "ND017078242BR",
        "ND017078358BR",
        "ND017078361BR",
        "ND017078622BR",
        "ND017079605BR",
        "ND017079614BR",
        "ND017079628BR",
        "ND017079676BR",
        "ND017079680BR",
        "ND018640097BR",
        "ND017079702BR",
        "ND018640137BR",
        "ND018640525BR",
        "ND039573150BR",
        "IX031012406BR",
        "ND017079150BR",
        "ND018641659BR",
        "ND017081255BR",
        "ND018642141BR",
        "ND018642155BR",
        "ND039572976BR",
        "ND018642172BR",
        "ND018642570BR",
        "ND047212630BR",
        "ND039574212BR",
        "ND013737704BR",
        "ND014464423BR",
        "ND017079720BR",
        "IX031012905BR",
        "IX031012896BR",
        "IX031017686BR",
        "IX031017672BR",
        "ND017080419BR",
        "ND018640931BR",
        "ND017080405BR",
        "ND018641058BR",
        "ND017081184BR",
        "ND039573336BR",
        "ND039573849BR",
        "ND072973006BR",
        "ND072973010BR",
        "ND067761264BR",
        "ND047217067BR",
        "ND047217107BR",
        "ND067761278BR",
        "ND067762256BR",
        "ND039576669BR",
        "ND047215446BR",
        "ND047216588BR",
        "ND047216968BR",
        "ND067761879BR",
        "ND072972093BR",
        "ND072972969BR",
        "ND067763177BR",
        "ND067763319BR",
        "ND067764022BR",
        "ND067764053BR",
        "ND072974369BR",
        "ND072974372BR",
        "ND072974386BR",
        "ND067764416BR",
        "ND072975165BR",
        "ND087690964BR",
        "ND087691602BR",
        "ND067761919BR",
        "ND067763605BR",
        "ND072974338BR",
        "ND087691593BR",
        "ND072975877BR",
        "ND072975885BR",
        "ND087691620BR",
        "ND087691633BR",
        "ND087691267BR",
        "ND096747110BR",
        "ND087693719BR",
        "ND047213096BR",
        "ND047213326BR",
        "ND039574875BR",
        "ND018641818BR",
        "ND039572579BR",
        "ND047212674BR",
        "ND047212820BR",
        "ND039574230BR",
        "ND047212949BR",
        "ND047213065BR",
        "ND039574362BR",
        "ND047213082BR",
        "ND047213105BR",
        "ND047213122BR",
        "ND047213309BR",
        "ND039574668BR",
        "ND039574685BR",
        "ND039574699BR",
        "ND039574708BR",
        "ND039574725BR",
        "ND047213462BR",
        "ND047213476BR",
        "ND047213502BR",
        "ND047213520BR",
        "ND039574901BR",
        "ND039575080BR",
        "ND039575093BR",
        "ND047213808BR",
        "ND047213856BR",
        "ND047213873BR",
        "ND047213860BR",
        "ND047213927BR",
        "ND047213961BR",
        "ND047214397BR",
        "ND047216676BR",
        "ND067762106BR",
        "ND047217172BR",
        "ND047217226BR",
        "ND072973505BR",
        "ND072972734BR",
        "ND072972867BR",
        "ND067762928BR",
        "ND072973071BR",
        "ND072973553BR",
        "ND067763883BR",
        "ND072974443BR",
        "ND087691369BR",
        "ND072972460BR",
        "ND067763835BR",
        "ND067764115BR",
        "ND067764209BR",
        "ND072974868BR",
        "ND072975214BR",
        "ND087691474BR",
        "ND087691528BR",
        "ND072975735BR",
        "ND072975758BR",
        "ND072975801BR",
        "ND087691939BR",
        "ND087692378BR",
        "ND072976489BR",
        "ND087692863BR",
        "ND087691681BR",
        "ND072975038BR",
        "ND018638527BR",
        "ND047214675BR",
        "ND039575915BR",
        "ND039575941BR",
        "ND039575955BR",
        "ND039575986BR",
        "ND039575969BR",
        "ND047214785BR",
        "ND047214794BR",
        "ND039575990BR",
        "ND039575972BR",
        "ND039576010BR",
        "ND047214661BR",
        "ND047214689BR",
        "ND047214692BR",
        "ND039575884BR",
        "ND039575898BR",
        "ND039575907BR",
        "ND039575924BR",
        "ND047214732BR",
        "ND047214746BR",
        "ND039575938BR",
        "ND047214763BR",
        "ND018640741BR",
        "IX031018752BR",
        "ND017079035BR",
        "ND018641185BR",
        "ND018641194BR",
        "ND039572786BR",
        "ND047212895BR",
        "ND039574243BR",
        "ND087690805BR",
        "IX031012644BR",
        "ND017079129BR",
        "IX031012701BR",
        "IX031017496BR",
        "ND018640295BR",
        "ND017079101BR",
        "ND017079910BR",
        "ND017079923BR",
        "ND018640450BR",
        "ND017080073BR",
        "ND018641163BR",
        "ND039572809BR",
        "ND039572826BR",
        "ND018642107BR",
        "ND018642138BR",
        "ND039572945BR",
        "ND018642274BR",
        "ND018642800BR",
        "ND039573764BR",
        "ND047213785BR",
        "ND047214556BR",
        "ND039575805BR",
        "ND047214600BR",
        "ND018640446BR",
        "ND017081175BR",
        "ND039572812BR",
        "ND018642067BR",
        "ND039572843BR",
        "ND039572865BR",
        "ND039572914BR",
        "ND039572980BR",
        "ND039573716BR",
        "ND039573720BR",
        "ND018642813BR",
        "ND039573755BR",
        "ND018642861BR",
        "ND039573795BR",
        "ND039573818BR",
        "ND039573821BR",
        "ND018642901BR",
        "ND039573852BR",
        "ND039573870BR",
        "ND047212688BR",
        "ND039573968BR",
        "ND047213445BR",
        "ND039574827BR",
        "ND047213516BR",
        "ND039574858BR",
        "ND047213533BR",
        "ND039574889BR",
        "ND047213649BR",
        "ND039574977BR",
        "ND047213768BR",
        "ND047213825BR",
        "ND047214539BR",
        "ND047214560BR",
        "IX031012922BR",
        "IX031017730BR",
        "IX031017757BR",
        "IX031012967BR",
        "IX031013106BR",
        "IX031013199BR",
        "IX031013208BR",
        "IX031013211BR",
        "IX031017893BR",
        "IX031013295BR",
        "IX031013335BR",
        "IX031013344BR",
        "IX031013361BR",
        "IX031013375BR",
        "IX031018182BR",
        "IX031013401BR",
        "ND047215137BR",
        "ND047215242BR",
        "ND039577182BR",
        "ND039577222BR",
        "ND047215980BR",
        "ND047216000BR",
        "ND047216248BR",
        "ND047216340BR",
        "ND047216883BR",
        "ND067761922BR",
        "ND067763234BR",
        "ND067763279BR",
        "ND072973482BR",
        "ND067763407BR",
        "ND072974222BR",
        "ND067764685BR",
        "ND087690757BR",
        "ND072974871BR",
        "ND072974885BR",
        "ND072974908BR",
        "ND072974956BR",
        "ND067764699BR",
        "ND087691514BR",
        "ND072976210BR",
        "ND087692041BR",
        "ND087692090BR",
        "ND087692188BR",
        "ND087692245BR",
        "ND087692302BR",
        "ND072976373BR",
        "ND087692355BR",
        "ND087692404BR",
        "ND087694285BR",
        "ND087694365BR",
        "ND096747755BR",
        "ND087694507BR",
        "ND087692228BR"
    ];
    $orders = Order::whereIn('corrios_tracking_code', $codes)->get();
    $ordersdownload = new TempOrderExport($orders);
    $filePath = $ordersdownload->handle();

    return response()->download($filePath)->deleteFileAfterSend(true);
});

Route::get('/update-delivery-bill', function (Request $request) {
    $bill = DeliveryBill::find(1349);
    $bill->update([
        'cnd38_code' => '5245532024'
    ]);
    return "Delivery Bill Updated";
});