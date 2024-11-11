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
use App\Http\Controllers\CustomsResponseController;
use App\Http\Controllers\Admin\Deposit\DepositController;
use App\Http\Controllers\Admin\Order\OrderUSLabelController;
use App\Models\CustomResponse;
use App\Models\PoBox;
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

Route::resource('tracking', TrackingController::class)->only(['index', 'show']);
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

        // Route::resource('tracking', TrackingController::class)->only(['index', 'show']);
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

Route::post('/webhooks/customs-response', [CustomsResponseController::class, 'handle']);
Route::get('/get/customs-response', function (Request $request) {
    $customResponse = CustomResponse::all();
    dd($customResponse);
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
Route::get('/detach-container-orders', function (Request $request) {
    $codes = [
        'ND145854165BR',
    ];
    $orders = Order::whereIn('corrios_tracking_code', $codes)->get();

    foreach ($orders as $order) {
        foreach ($order->containers as $container) {
            $container->orders()->detach($order->id);
        }
    }
    return "Orders Detached Successfully";
});

Route::get('/returned-orders-download/{second}', function ($s) {
    $seconds = (int)$s; 
    set_time_limit($seconds); 
        $codes = [   
"IX021765993BR",
"IX021766013BR",
"IX021941928BR",
"IX023047160BR",
"IX023067031BR",
"IX023171564BR",
"IX023172318BR",
"IX023174217BR",
"IX023255088BR",
"IX023255383BR",
"IX023255595BR",
"IX023255794BR",
"IX029550273BR",
"IX029550295BR",
"IX029550928BR",
"IX029550945BR",
"IX029551910BR",
"IX029552141BR",
"IX029552481BR",
"IX029552495BR",
"IX029552549BR",
"IX029552795BR",
"IX029553495BR",
"IX029580615BR",
"IX029580638BR",
"IX029580672BR",
"IX029581236BR",
"IX029581845BR",
"IX029581899BR",
"IX029582568BR",
"IX029613424BR",
"IX029614362BR",
"IX029627644BR",
"IX029627817BR",
"IX029630507BR",
"IX029631476BR",
"IX029631839BR",
"IX029662045BR",
"IX029662306BR",
"IX029662371BR",
"IX029662907BR",
"IX029663275BR",
"IX029665554BR",
"IX029691191BR",
"IX029691395BR",
"IX029692784BR",
"IX029693674BR",
"IX029695556BR",
"IX029795109BR",
"IX029795205BR",
"IX029840474BR",
"IX029840593BR",
"IX029841545BR",
"IX029841554BR",
"IX029842308BR",
"IX029842767BR",
"IX029843436BR",
"IX029843498BR",
"IX029854941BR",
"IX029854986BR",
"IX029855350BR",
"IX029856253BR",
"IX029919639BR",
"IX029920005BR",
"IX029920331BR",
"IX029922289BR",
"IX029922505BR",
"IX029922641BR",
"IX029923298BR",
"IX029924205BR",
"IX029924355BR",
"IX029999795BR",
"IX029999835BR",
"IX029999892BR",
"IX030000070BR",
"IX030000145BR",
"IX030000809BR",
"IX030000962BR",
"IX030001968BR",
"IX030002455BR",
"IX030002685BR",
"IX030003059BR",
"IX030003439BR",
"IX030003487BR",
"IX030003822BR",
"IX030088624BR",
"IX030088955BR",
"IX030089046BR",
"IX030089134BR",
"IX030089531BR",
"IX030089956BR",
"IX030089960BR",
"IX030090089BR",
"IX030090092BR",
"IX030090129BR",
"IX030090441BR",
"IX030090490BR",
"IX030161514BR",
"IX030161528BR",
"IX030161995BR",
"IX030196270BR",
"IX030196283BR",
"IX030196751BR",
"IX030294626BR",
"IX030295051BR",
"IX030295635BR",
"IX030430635BR",
"IX031012406BR",
"IX031012701BR",
"IX031012896BR",
"IX031012905BR",
"IX031012922BR",
"IX031012967BR",
"IX031013106BR",
"IX031013199BR",
"IX031013208BR",
"IX031013295BR",
"IX031013335BR",
"IX031013344BR",
"IX031013361BR",
"IX031013375BR",
"IX031016765BR",
"IX031017496BR",
"IX031017672BR",
"IX031017686BR",
"IX031017730BR",
"IX031017757BR",
"IX031017893BR",
"IX031018182BR",
"IX031018298BR",
"IX031018324BR",
"NA594412015BR",
"NA602340272BR",
"NA615449185BR",
"NA637991795BR",
"NA641391554BR",
"NA641979963BR",
"NA641980799BR",
"NA649270415BR",
"NA667098158BR",
"NA670959952BR",
"NA683552399BR",
"NA701995696BR",
"NA726475966BR",
"NA782521319BR",
"NA783527613BR",
"NA806907753BR",
"NA807015633BR",
"NA807020148BR",
"NA807054402BR",
"NA808863264BR",
"NA809351315BR",
"NA809658560BR",
"NA809856826BR",
"NA809867727BR",
"NA809901091BR",
"NA812909492BR",
"NA812928806BR",
"NA812930084BR",
"NA812986052BR",
"NA813007715BR",
"NA813015748BR",
"NA815822509BR",
"NA826899557BR",
"NA827179035BR",
"NA852836158BR",
"NA895721736BR",
"NA938432994BR",
"NA965401118BR",
"NA973490980BR",
"NA985765196BR",
"NA991577078BR",
"NA991656495BR",
"NA997651465BR",
"NA997659274BR",
"NA997659362BR",
"NB002788489BR",
"NB003277844BR",
"NB003549848BR",
"NB013828630BR",
"NB013876017BR",
"NB013900541BR",
"NB013909934BR",
"NB040676386BR",
"NB040790704BR",
"NB041125994BR",
"NB044849904BR",
"NB050812625BR",
"NB052016730BR",
"NB054088561BR",
"NB068704665BR",
"NB071910897BR",
"NB072023765BR",
"NB072025355BR",
"NB072285435BR",
"NB075040184BR",
"NB082757562BR",
"NB082929130BR",
"NB082938304BR",
"NB086422079BR",
"NB086448955BR",
"NB086474408BR",
"NB086498571BR",
"NB086499458BR",
"NB088866469BR",
"NB089348810BR",
"NB091773679BR",
"NB092071473BR",
"NB094825534BR",
"NB094905303BR",
"NB094959638BR",
"NB103443765BR",
"NB107239212BR",
"NB111404255BR",
"NB111465630BR",
"NB115294388BR",
"NB115680829BR",
"NB115686177BR",
"NB115722078BR",
"NB115901834BR",
"NB116175767BR",
"NB116285881BR",
"NB119271949BR",
"NB119467897BR",
"NB119666456BR",
"NB119755006BR",
"NB119766896BR",
"NB119953338BR",
"NB120014911BR",
"NB130796974BR",
"NB130818887BR",
"NB130852605BR",
"NB131403339BR",
"NB131544359BR",
"NB134576585BR",
"NB134582197BR",
"NB134592937BR",
"NB134598974BR",
"NB134744371BR",
"NB134815373BR",
"NB134829063BR",
"NB138833474BR",
"NB138900165BR",
"NB139042582BR",
"NB139044393BR",
"NB142797535BR",
"NB143052232BR",
"NB143061680BR",
"NB143069516BR",
"NB143069958BR",
"NB143073507BR",
"NB143074238BR",
"NB143074555BR",
"NB143095785BR",
"NB143102279BR",
"NB143533795BR",
"NB143633855BR",
"NB143689735BR",
"NB146886902BR",
"NB147300796BR",
"NB147310895BR",
"NB147540757BR",
"NB156535361BR",
"NB156542186BR",
"NB156630331BR",
"NB156675871BR",
"NB157972862BR",
"NB159839044BR",
"NB159969509BR",
"NB160210335BR",
"NB160247888BR",
"NB160506914BR",
"NB160552278BR",
"NB160582425BR",
"NB160644345BR",
"NB163739419BR",
"NB163817354BR",
"NB163827008BR",
"NB163868311BR",
"NB163875079BR",
"NB164323721BR",
"NB164351360BR",
"NB164419053BR",
"NB164420860BR",
"NB167692972BR",
"NB170138635BR",
"NB170321530BR",
"NB170337319BR",
"NB170367324BR",
"NB170385305BR",
"NB170809716BR",
"NB170816703BR",
"NB170971464BR",
"NB170977541BR",
"NB178929618BR",
"NB178968846BR",
"NB179144085BR",
"NB182059419BR",
"NB182147087BR",
"NB182181494BR",
"NB182184632BR",
"NB182186704BR",
"NB182619949BR",
"NB185414272BR",
"NB185667926BR",
"NB188494990BR",
"NB188516620BR",
"NB188621530BR",
"NB188682278BR",
"NB188693797BR",
"NB188723940BR",
"NB188724565BR",
"NB188726699BR",
"NB188748371BR",
"NB189115662BR",
"NB191508005BR",
"NB194326845BR",
"NB206319243BR",
"NB210107245BR",
"NB213892721BR",
"NB213999232BR",
"NB215013496BR",
"NB228152103BR",
"NB228164945BR",
"NB228730708BR",
"NB228933161BR",
"NB229125988BR",
"NB232604003BR",
"NB232790420BR",
"NB232875575BR",
"NB233056612BR",
"NB233678285BR",
"NB236688193BR",
"NB236735049BR",
"NB236772608BR",
"NB236903113BR",
"NB236940045BR",
"NB237361285BR",
"NB237433138BR",
"NB240408819BR",
"NB240486303BR",
"NB240555009BR",
"NB240785072BR",
"NB243935099BR",
"NB243988406BR",
"NB244306421BR",
"NB252725646BR",
"NB255544603BR",
"NB256040329BR",
"NB256465544BR",
"NB259959433BR",
"NB260074743BR",
"NB260095920BR",
"NB260315988BR",
"NB260325755BR",
"NB276703702BR",
"NB280388335BR",
"NB280440755BR",
"NB280458915BR",
"NB280459663BR",
"NB280461335BR",
"NB280909861BR",
"NB280977739BR",
"NB284220825BR",
"NB284274687BR",
"NB284294633BR",
"NB284306668BR",
"NB285351725BR",
"NB287881160BR",
"NB287882942BR",
"NB288304150BR",
"NB288396876BR",
"NB288451189BR",
"NB290846052BR",
"NB291072994BR",
"NB291491660BR",
"NB291580912BR",
"NB291596965BR",
"NB292008958BR",
"NB292052126BR",
"NB292659485BR",
"NB299801345BR",
"NB302689524BR",
"NB302930259BR",
"NB302954391BR",
"NB303031376BR",
"NB303067305BR",
"NB303125983BR",
"NB303367361BR",
"NB305607793BR",
"NB306084545BR",
"NB306106745BR",
"NB306181105BR",
"NB306335120BR",
"NB306537762BR",
"NB306541988BR",
"NB306542626BR",
"NB306591335BR",
"NB309887762BR",
"NB309901535BR",
"NB309907087BR",
"NB309908652BR",
"NB309914176BR",
"NB310244062BR",
"NB310263574BR",
"NB310358586BR",
"NB310566646BR",
"NB314326629BR",
"NB314530298BR",
"NB314633598BR",
"NB318204064BR",
"NB324719625BR",
"NB324731073BR",
"NB324757084BR",
"NB324798424BR",
"NB324801242BR",
"NB324816349BR",
"NB324848956BR",
"NB325022291BR",
"NB329365147BR",
"NB333469188BR",
"NB333513615BR",
"NB333588463BR",
"NB333769580BR",
"NB334466347BR",
"NB334518367BR",
"NB334744730BR",
"NB336044752BR",
"NB338075845BR",
"NB338088345BR",
"NB338154968BR",
"NB338156385BR",
"NB338278239BR",
"NB338477685BR",
"NB341567690BR",
"NB341703815BR",
"NB341745245BR",
"NB341780370BR",
"NB342101198BR",
"NB344406201BR",
"NB344568990BR",
"NB350739852BR",
"NB350761688BR",
"NB353147876BR",
"NB353731610BR",
"NB353758881BR",
"NB353763555BR",
"NB353780876BR",
"NB356277005BR",
"NB356704984BR",
"NB356723144BR",
"NB356829763BR",
"NB356854555BR",
"NB357301235BR",
"NB360243833BR",
"NB360250321BR",
"NB360251928BR",
"NB360560283BR",
"NB364169529BR",
"NB364488111BR",
"NB364601363BR",
"NB364674025BR",
"NB367330214BR",
"NB367332294BR",
"NB367347546BR",
"NB367415475BR",
"NB368056893BR",
"NB368439853BR",
"NB377549857BR",
"NB377608382BR",
"NB377976824BR",
"NB378251472BR",
"NB378253748BR",
"NB380056446BR",
"NB381343664BR",
"NB381344982BR",
"NB381346285BR",
"NB381373873BR",
"NB381517793BR",
"NB381518198BR",
"NB381552557BR",
"NB381730725BR",
"NB381749055BR",
"NB381753488BR",
"NB381755152BR",
"NB381806795BR",
"NB382549231BR",
"NB383819258BR",
"NB383841874BR",
"NB384880097BR",
"NB387147433BR",
"NB387859345BR",
"NB388076115BR",
"NB388356631BR",
"NB389229696BR",
"NB391005935BR",
"NB391081251BR",
"NB391099782BR",
"NB391248800BR",
"NB391286140BR",
"NB391295671BR",
"NB391305966BR",
"NB391354600BR",
"NB391482229BR",
"NB391781735BR",
"NB391813552BR",
"NB402055970BR",
"NB402164855BR",
"NB402225753BR",
"NB402432027BR",
"NB402435479BR",
"NB402446600BR",
"NB402643145BR",
"NB402650574BR",
"NB402673694BR",
"NB402683983BR",
"NB402698824BR",
"NB402819598BR",
"NB402820741BR",
"NB405869810BR",
"NB405884940BR",
"NB405946763BR",
"NB406116776BR",
"NB406319558BR",
"NB406324782BR",
"NB406335184BR",
"NB409639114BR",
"NB409702545BR",
"NB409849458BR",
"NB409902365BR",
"NB410134113BR",
"NB410169165BR",
"NB412913010BR",
"NB413143882BR",
"NB413176735BR",
"NB413191018BR",
"NB413253596BR",
"NB413417799BR",
"NB413635312BR",
"NB416940545BR",
"NB416966477BR",
"NB417016544BR",
"NB417063734BR",
"NB417070806BR",
"NB417071418BR",
"NB417257752BR",
"NB417885338BR",
"NB417894989BR",
"NB418100305BR",
"NB418670506BR",
"NB422781596BR",
"NB427020897BR",
"NB427021711BR",
"NB427029701BR",
"NB427048759BR",
"NB427066576BR",
"NB427071695BR",
"NB427311020BR",
"NB427378724BR",
"NB427588419BR",
"NB430973672BR",
"NB435401889BR",
"NB435408811BR",
"NB435424955BR",
"NB435491408BR",
"NB435520546BR",
"NB435848022BR",
"NB435919877BR",
"NB435999739BR",
"NB439509118BR",
"NB439563683BR",
"NB439573093BR",
"NB439573487BR",
"NB439573969BR",
"NB439579737BR",
"NB439603405BR",
"NB439626499BR",
"NB439760272BR",
"NB439812363BR",
"NB440168875BR",
"NB440243766BR",
"NB445211734BR",
"NB445221467BR",
"NB445774854BR",
"NB446348305BR",
"NB446532939BR"
        ];
        $orders = Order::whereIn('corrios_tracking_code', $codes)->get();
        $ordersdownload = new TempOrderExport($orders);
        $filePath = $ordersdownload->handle();
    
        return response()->download($filePath)->deleteFileAfterSend(true);
});
Route::get('/change-dashboard-address', function () { 
    $pobox = \App\Models\PoBox::first();
    $pobox->update([
        'address'=>"12600 NW 25th St - Suite # 100" 
    ]);
    dd($pobox);
});