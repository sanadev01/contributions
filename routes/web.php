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
use App\Models\BillingInformation;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use App\Services\PasarEx\CainiaoService;

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
    $shop = "https://" . request()->shop;
    if (request()->has('shop')) {
        $redirectUri = $shopifyClient->getRedirectUrl(request()->shop, [
            'connect_name' => request()->shop,
            'connect_store_url' => $shop
        ]);
        return redirect()->away($redirectUri);
    }
    return redirect('login');
});
ini_set('memory_limit', '10000M');
ini_set('memory_limit', '-1');
Route::get('tax-calculator', [TaxCalculatorController::class,'index'])->name('tax-calculator.index')->middleware('auth');

// Route::resource('tracking', TrackingController::class)->only(['index', 'show']);
Route::get('/home', function () {

    if (session()->get('shopify.redirect')) {
        return redirect(session()->get('shopify.redirect'));
    }

    return redirect()->route('admin.home');
});
Route::get('verify', 'Auth\TwoFactorVerificationController@showVerificationForm')->name('showVerificationForm');
Route::post('verify', 'Auth\TwoFactorVerificationController@verifyToken')->name('verifyToken');

Auth::routes();

Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');


Route::namespace('Admin')->middleware(['auth'])->as('admin.')->group(function () {

    Route::get('dashboard', 'HomeController')->name('home');
    Route::get('dashboard-test', 'HomeTestController');
    Route::resource('parcels', PreAlertController::class);
    Route::get('parcel/{order}/duplicate', DuplicatePreAlertController::class)->name('parcel.duplicate');
    Route::resource('billing-information', BillingInformationController::class);
    // Route::resource('import-excel', ImportExcelController::class)->only(['index','store']);

    Route::resource('handling-services', HandlingServiceController::class)->except('show');
    Route::resource('addresses', AddressController::class);
    Route::get('addresses-export', [\App\Http\Controllers\Admin\AddressController::class, 'exportAddresses'])->name('export.addresses');
    Route::resource('shipping-services', ShippingServiceController::class);

    Route::namespace('Import')->prefix('import')->as('import.')->group(function () {
        Route::resource('import-excel', ImportExcelController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::resource('import-order', ImportOrderController::class)->only(['index', 'store', 'show', 'edit', 'destroy']);
    });

    Route::resource('orders', OrderController::class)->only('index', 'destroy', 'show');
    Route::resource('trash-orders', TrashOrderController::class)->only(['index', 'destroy']);

    Route::resource('tracking', TrackingController::class)->only(['index', 'show']);
    Route::get('/buy-usps-label', [\App\Http\Controllers\Admin\Order\OrderUSPSLabelController::class, 'uspsBulkView'])->name('bulk-usps-label');

    Route::namespace('Order')->group(function () {
        Route::resource('leve-order-import', LeveOrderImportController::class)->only(['index', 'store']);
        Route::get('orders/{order}/duplicate', DuplicateOrderController::class)->name('orders.duplicate');
        Route::resource('orders.sender', OrderSenderController::class)->only('index', 'store');
        Route::resource('orders.recipient', OrderRecipientController::class)->only('index', 'store');
        Route::resource('orders.services', OrderServicesController::class)->only('index', 'store');
        Route::resource('orders.order-details', OrderItemsController::class)->only('index', 'store');
        Route::resource('orders.order-invoice', OrderInvoiceController::class)->only('index', 'store');
        Route::resource('orders.label', OrderLabelController::class)->only('index', 'store');
        Route::get('order-exports', OrderExportController::class)->name('order.exports');
        Route::get('bulk-action', BulkActionController::class)->name('order.bulk-action');
        Route::get('pre-alert', PreAlertMailController::class)->name('order.pre-alert');
        Route::get('consolidate-domestic-label', ConsolidateDomesticLabelController::class)->name('order.consolidate-domestic-label');
        Route::get('order/{order}/us-label', [OrderUSLabelController::class, 'index'])->name('order.us-label.index');
        Route::resource('orders.usps-label', OrderUSPSLabelController::class)->only('index', 'store');
        Route::resource('orders.ups-label', OrderUPSLabelController::class)->only('index', 'store');
        Route::post('order/update/status', OrderStatusController::class)->name('order.update.status');
        Route::get('gde/{order}/invoice', GDEInvoiceDownloadController::class)->name('gde.invoice.download');

        Route::get('order-ups-label-cancel-pickup/{id?}', [\App\Http\Controllers\Admin\Order\OrderUPSLabelController::class, 'cancelUPSPickup'])->name('order.ups-label.cancel.pickup');
    });
    //Cancel Lable Route for GePS & Prime5
    Route::get('order/{order}/cancel-label', [\App\Http\Controllers\Admin\Order\OrderLabelController::class, 'cancelLabel'])->name('order.label.cancel');

    Route::namespace('Consolidation')->prefix('consolidation')->as('consolidation.')->group(function () {
        Route::resource('parcels', SelectPackagesController::class)->only('index', 'store', 'edit', 'update');
        Route::resource('parcels.services', ServicesController::class)->only('index', 'store');
    });

    Route::namespace('Payment')->group(function () {
        Route::resource('payment-invoices', PaymentInvoiceController::class)->only(['index', 'store', 'destroy']);
        Route::prefix('payment-invoices')->as('payment-invoices.')->group(function () {
            Route::resource('orders', OrdersSelectController::class)->only(['index', 'store']);
            Route::resource('invoice', OrdersInvoiceController::class)->only(['show', 'store', 'edit', 'update']);
            Route::resource('invoice.checkout', OrdersCheckoutController::class)->only(['index', 'store']);
            Route::get('invoice/{invoice}/toggle_paid', \PaymentStatusToggleController::class)->name('paid.toggle');
            Route::get('invoice/{invoice}/toggle_type', \PaymentTypeToggleController::class)->name('type.toggle');
            Route::get('postpaid/{invoice}/export', \PostPaidInvoiceExportController::class)->name('postpaid.export');
            Route::get('exports', PaymentInvoiceExportController::class)->name('exports');
        });
    });

    Route::namespace('Rates')->prefix('rates')->as('rates.')->group(function () {
        Route::resource('profit-packages', ProfitPackageController::class);
        Route::resource('fixed-charges', FixedChargesController::class)->only(['index', 'store']);
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
        Route::resource('profit-packages-upload', ProfitPackageUploadController::class)->only(['create', 'store', 'edit', 'update']);
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

    Route::namespace('Connect')->group(function () {
        Route::resource('connect', ConnectController::class)->only(['index', 'create', 'edit', 'update', 'destroy']);
        Route::prefix('connect')->as('connect.')->group(function () {
            Route::get('/guide', \GuideController::class)->name('guide');
            Route::resource('shopify', ConnectShopifyController::class)->only(['create', 'store']);
        });
    });

    Route::resource('settings', SettingController::class)->only(['index', 'store']);
    Route::resource('profile', ProfileController::class)->only(['index', 'store']);
    Route::resource('users', UserController::class)->only(['index', 'destroy']);
    Route::post('users/export', UserExportController::class)->name('users.export.index');
    Route::resource('users.setting', UserSettingController::class)->only('index', 'store');
    Route::resource('shcode', ShCodeController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('shcode-export', ShCodeImportExportController::class)->only(['index', 'create', 'store']);
    Route::get('shcode-export/{type?}', [ShCodeImportExportController::class, 'index'])->name('admin.shcode-export.index');

    Route::namespace('Tax')->group(function () {
        Route::resource('tax', TaxController::class)->except(['show', 'destroy']);
        Route::post('refund-tax', [App\Http\Controllers\Admin\Tax\TaxController::class, 'refund'])->name('refund-tax');
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
            Route::get('sample-exports/{package}/{regionRates?}', SampleRateDownloadController::class)->name('sample.exports');
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
    Route::namespace('Adjustment')->group(function () {
        Route::resource('adjustment', AdjustmentController::class)->except(['index', 'show', 'destroy']);
    });

    Route::resource('roles', RoleController::class);
    Route::resource('roles.permissions', RolePermissionController::class);

    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/close', [\App\Http\Controllers\Admin\TicketController::class, 'markClose'])->name('ticket.mark-closed');

    Route::namespace('Reports')->as('reports.')->prefix('reports')->group(function () {
        Route::resource('user-shipments', \ShipmentPerUserReportController::class)->only(['index', 'create']);
        Route::resource('order-trackings', TrackingReportController::class)->only(['index', 'store']);
        Route::resource('order', OrderReportController::class)->only(['index', 'create']);
        Route::resource('commission', CommissionReportController::class)->only(['index', 'show']);
        Route::resource('audit-report', AuditReportController::class)->only(['index', 'create']);
        Route::resource('anjun', AnjunReportController::class)->only(['index', 'create']);
        Route::resource('kpi-report', KPIReportController::class)->only(['index', 'store']);
        Route::get('tax-report', TaxReportController::class)->name('tax-report');
    });
    Route::get('export-orders', [App\Http\Controllers\Admin\Reports\OrderReportController::class, 'download'])->name('reports.export-orders');
    Route::get('unpaid-orders-report', [App\Http\Controllers\Admin\Reports\UnPaidOrdersController::class, 'index'])->name('reports.unpaid-orders');
    Route::post('unpaid-orders-download', [App\Http\Controllers\Admin\Reports\UnPaidOrdersController::class, 'download'])->name('reports.unpaid-orders-download');

    Route::namespace('Inventory')->as('inventory.')->prefix('inventory')->group(function () {
        Route::resource('product', ProductController::class);
        Route::get('products/pickup', [\App\Http\Controllers\Admin\Inventory\ProductController::class, 'pickup'])->name('product.pickup');
        Route::post('product/status', [\App\Http\Controllers\Admin\Inventory\ProductController::class, 'statusUpdate'])->name('status.update');
        Route::resource('product-export', ProductExportController::class)->only('index');
        Route::resource('product-import', ProductImportController::class)->only(['create', 'store']);
        Route::resource('product-order', ProductOrderController::class)->only('show', 'create', 'store');
        // Route::resource('inventory-orders', InventoryOrderController::class)->only('index','store');

        Route::get('inventory-orders', [\App\Http\Controllers\Admin\Inventory\InventoryOrderController::class, 'index'])->name('orders');
        Route::get('inventory-orders-export', [\App\Http\Controllers\Admin\Inventory\InventoryOrderController::class, 'exportOrders'])->name('orders.export');
    });

    Route::namespace('Affiliate')->as('affiliate.')->prefix('affiliate')->group(function () {
        Route::resource('dashboard', DashboardController::class)->only('index');
        Route::resource('sales-commission', SalesCommisionController::class)->only(['index', 'create', 'destroy']);
        Route::get('sale-exports', SaleExportController::class)->name('sale.exports');
    });

    Route::namespace('Label')->as('label.')->prefix('label')->group(function () {
        Route::resource('scan', PrintLabelController::class)->only('create', 'show', 'store', 'update');
    });

    Route::resource('liability', Deposit\LiabilityController::class)->only('create', 'store', 'index');

    Route::resource('deposit', Deposit\DepositController::class)->only('create', 'store', 'index');
    Route::get('download-deposit-attachment/{attachment?}', [DepositController::class, 'downloadAttachment'])->name('download_attachment');
    Route::get('view-deposit-description/{deposit?}', [DepositController::class, 'showDescription'])->name('deposit.description');
    Route::post('update/deposit/description/{deposit?}', [DepositController::class, 'updateDescription'])->name('deposit.description.update');


    Route::namespace('Activity')->as('activity.')->prefix('activity')->group(function () {
        Route::resource('log', ActivityLogController::class)->only('index');
    });

    Route::post('users/{user}/login', AnonymousLoginController::class)->name('users.login');

    Route::post('ajax/get-states', AjaxCallController::class)->name('ajax.state')->withoutMiddleware(['auth']);

    Route::get('language/{locale}', LanguageController::class)->name('locale.change');

    Route::namespace('Modals')->prefix('modals')->as('modals.')->group(function () {
        Route::get('user/suspended', \UserSuspendController::class)->name('user.suspended');
        Route::get('parcel/{parcel}/shipment-info', \ShipmentModalController::class)->name('parcel.shipment-info');
        Route::get('report/{user}/shipment-user', \ShipmentByServiceController::class)->name('report.shipment-user');
        Route::get('parcel/{parcel}/consolidation-print', \ConsolidationPrintController::class)->name('parcel.consolidation-print');
        Route::get('order/{order}/invoice', \OrderInvoiceModalController::class)->name('order.invoice');
        Route::get('commissions', \CommissionModalController::class)->name('order.commissions');
        Route::get('order/{error}/edit/{edit?}', [\App\Http\Controllers\Admin\Modals\ImportOrderModalController::class, 'edit'])->name('order.error.edit');
        Route::get('order/{error}/show', [\App\Http\Controllers\Admin\Modals\ImportOrderModalController::class, 'show'])->name('order.error.show');
        Route::get('package/{package}/users', [\App\Http\Controllers\Admin\Rates\ProfitPackageController::class, 'packageUsers'])->name('package.users');
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
Route::namespace('Admin\Webhooks')->prefix('webhooks')->as('admin.webhooks.')->group(function () {
    Route::namespace('Shopify')->prefix('shopify')->as('shopify.')->group(function () {
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

Route::get('order/{id}/label/get', \Admin\Label\GetLabelController::class)->name('order.label.download');

Route::get('order/{order}/us-label/get', function (App\Models\Order $order) {
    if (!file_exists(storage_path("app/labels/{$order->us_api_tracking_code}.pdf"))) {
        return apiResponse(false, "Lable Expired or not generated yet please update lable");
    }
    return response()->download(storage_path("app/labels/{$order->us_api_tracking_code}.pdf"), "{$order->us_api_tracking_code} - {$order->warehouse_number}.pdf", [], 'inline');
})->name('order.us-label.download');

Route::get('permission', function ($id = null) {
    Artisan::call('db:seed --class=PermissionSeeder', ['--force' => true]);
    return Artisan::output();
});
Route::get('session-refresh/{slug?}', function ($slug = null) {
    if ($slug) {
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
Route::get('order-status-update/{order}/{status}', function (Order $order, $status) {
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

Route::get('/update-order-tracking', [UpdateTracking::class, 'update']);


Route::get('/download-updated-tracking', [DownloadUpdateTracking::class, 'download']);

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
        ->update(['is_paid' => true]);

    return 'Status Updated';
});

Route::get('/truncate-shcodes', function () {
    DB::table('sh_codes')->truncate();
    return 'ShCode table truncated successfully!';
});
Route::get('/warehouse-detail/{warehouse}/{field}', function ($warehouse, $field) {
    $order = (Order::where('warehouse_number', $warehouse)->first());
    \Log::info(
        $order->toArray()
    );
    dump($order->update([$field => null]));
    dd($order);
});

Route::post('/webhooks/customs-response', [CustomsResponseController::class, 'handle']);
Route::get('/get/customs-response', function (Request $request) {
    $customResponse = CustomResponse::all();
    dd($customResponse);
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
Route::get('/warehouse-detail/{warehouse}', function ($warehouse) {
 
    dd(Order::where('warehouse_number', $warehouse)->first());  
});
 

Route::get('/create-order-test', function (CainiaoService $cainiaoService) {
  

    $result = $cainiaoService->createOrder();

    if ($result) {
        return response()->json(['success' => true, 'data' => $result]);
    } else {
        return response()->json(['success' => false, 'message' => 'Failed to create order']);
    }
});
Route::get('/waybill-get', function () {
    return testWaybill();
});
function testWaybill()
{

    $content = [
        "waybillType" => "1",
        "orderCode" => "LP00667794278588",
        "locale" => "zh_CN",
        "needSelfDrawLabels" => "true"
    ];
    $linkUrl = 'https://link.cainiao.com/gateway/custom/open_integration_test_env';
    $appSecret = '2A1X6281822ts3068j1F8Wdq99C76119';   // APPKEY对应的秘钥 
    $cpCode = 'de316159604032ef042935f43ffc3e2b';     //  调用方的CPCODE 
    $msgType = 'cnge.waybill.get';  // 调用的API名 
    $toCode = 'CGOP';        //  调用的目标TOCODE，有些接口TOCODE可以不用填写
    $digest = base64_encode(md5(json_encode($content) . $appSecret, true)); //生成签名  
    echo ('digest is ' . $digest);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkUrl);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
    $post_data = 'msg_type=' . $msgType
        . '&to_code=' . $toCode
        . '&logistics_interface=' . urlencode(json_encode($content))
        . '&data_digest=' . urlencode($digest)
        . '&logistic_provider_id=' . urlencode($cpCode);

    dump("Post body is: \n" . json_encode($post_data)) . "\n";
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_POST, 1);
    dump("Start to run...\n");
    $output = curl_exec($ch);
    curl_close($ch);
    dd("Finished, result data is: \n" .   ($output));
}



Route::get('/cnge-bigbag-create', function () {
    return cngeBigbagCreate();
    // response
    // {"data":{"bigBagCode":"LP00672701713818","bigBagTrackingNumber":"CNCAIPBRSAODDNL45832001150301"},"success":"true"}
});
function cngeBigbagCreate()
{

    $content = [
        "request"=>[
            "locale"=> "zh_cn",
            "weight"=> 150,
            "weightUnit"=> "g",
            "orderCodeList"=> [
                "LP00672306313219"
            ],
           "handoverParam"=>[
            "zipCode"=> "310000",
            "mobilePhone"=> "18666270000",
            "city"=> "杭州市",
            "addressId"=> "",
            "telephone"=> "",
            "street"=> "Chiang Village Street",
            "district"=> "",
            "name"=> "Hrich",
            "detailAddress"=> "西湖区蒋村街道龙湖天街",
            "country"=> "CN",
            "countryCode"=> "CN",
            "portCode"=> "GRU",
            "state"=> "浙江省",
            "email"=> ""
      ]
      ]

    ];
    $linkUrl = 'https://link.cainiao.com/gateway/custom/open_integration_test_env';
    $appSecret = '2A1X6281822ts3068j1F8Wdq99C76119';   // APPKEY对应的秘钥 
    $cpCode = 'de316159604032ef042935f43ffc3e2b';     //  调用方的CPCODE 
    $msgType = 'cnge.bigbag.create';  // 调用的API名 
    $toCode = 'CNPMS';        //  调用的目标TOCODE，有些接口TOCODE可以不用填写
    $digest = base64_encode(md5(json_encode($content) . $appSecret, true)); //生成签名  
    echo ('digest is ' . $digest);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkUrl);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
    $post_data = 'msg_type=' . $msgType
        . '&to_code=' . $toCode
        . '&logistics_interface=' . urlencode(json_encode($content))
        . '&data_digest=' . urlencode($digest)
        . '&logistic_provider_id=' . urlencode($cpCode);

    dump("Post body is: \n" . json_encode($post_data)) . "\n";
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_POST, 1);
    dump("Start to run...\n");
    $output = curl_exec($ch);
    curl_close($ch);
    dd("Finished, result data is: \n" .   ($output));
}


Route::get('/cnge-cn38-request', function () {
    return cngeCn38Request();
});
function cngeCn38Request()
{

    $content = [
            "ULDParam"=> [
                "ULDNoBatchNo"=> "SNU06032024220240011752",
                "ULDNo"=> "SNU060322024",
                "ULDType"=> "Q5",
                "ULDWeight"=> 0,
                "ULDWeightUnit"=> "KG",
                "bigBagList"=> [
                    "bigBagTrackingNumber"=> [
                        "CNCAIPBRSAODDNL45832001150301"
                    ]
                      ]],
            "airlineParam"=> [
                "airlineCode"=> "LA",
                "ETD"=> 1722620416000,
                "transportNo"=> "8119",
                "fromPortCode"=> "HKG",
                "toPortCode"=> "GRU"
            ],
            "operationParam"=> [
                "opCode"=> "0",
                "opLocation"=> "DHS",
                "opTime"=> "2024-08-22 17:52:29",
                "timeZone"=> "+8"
            ]
        ];
    $linkUrl = 'https://link.cainiao.com/gateway/custom/open_integration_test_env';
    $appSecret = '2A1X6281822ts3068j1F8Wdq99C76119';   // APPKEY对应的秘钥 
    $cpCode = 'de316159604032ef042935f43ffc3e2b';     //  调用方的CPCODE 
    $msgType = 'cnge.cn38.request';  // 调用的API名 
    $toCode = 'CGOP';        //  调用的目标TOCODE，有些接口TOCODE可以不用填写
    $digest = base64_encode(md5(json_encode($content) . $appSecret, true)); //生成签名  
    echo ('digest is ' . $digest);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkUrl);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
    $post_data = 'msg_type=' . $msgType
        . '&to_code=' . $toCode
        . '&logistics_interface=' . urlencode(json_encode($content))
        . '&data_digest=' . urlencode($digest)
        . '&logistic_provider_id=' . urlencode($cpCode);

    dump("Post body is: \n" . json_encode($post_data)) . "\n";
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_POST, 1);
    dump("Start to run...\n");
    $output = curl_exec($ch);
    curl_close($ch);
    echo ("Finished, result data is: \n" .   json_encode($output));
    dd("Finished, result data is: \n"  );
}

Route::get('/cnge-cn38-callback', function (Request $request) {
    return cngeCn38Callback();
});
function cngeCn38Callback()
{
    $linkUrl = 'https://link.cainiao.com/gateway/custom/open_integration_test_env';
     $ch = curl_init();
     $cpCode =('de316159604032ef042935f43ffc3e2b');
     $toCode="CGOP";
    curl_setopt($ch, CURLOPT_URL, $linkUrl);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
    $post_data = "logistic_provider_id=$cpCode&to_code=$toCode&logistics_interface=%7B%22cn38Url%22%3A%22https%3A%2F%2Fmawb-files.oss-cn-beijing.aliyuncs.com%2Fopen-cn38%2FSNU06032024220240011752%3FExpires%3D1755430034%26OSSAccessKeyId%3DLTAI5tAGpPDsff49X2dvNVdH%26Signature%3D0RyUhpcqv4s2olQOuBd4%252BWJjWh4%253D%22%2C%22printData%22%3A%22JVBERi0xLjQKJfbk%2FN8KMSAwIG9iago8PAovVHlwZSAvQ2F0YWxvZwovVmVyc2lvbiAvMS40Ci9QYWdlcyAyIDAgUgo%2BPgplbmRvYmoKMiAwIG9iago8PAovVHlwZSAvUGFnZXMKL0tpZHMgWzMgMCBSXQovQ291bnQgMQo%2BPgplbmRvYmoKMyAwIG9iago8PAovVHlwZSAvUGFnZQovTWVkaWFCb3ggWzAuMCAwLjAgNTk1LjAgODM5LjBdCi9QYXJlbnQgMiAwIFIKL0NvbnRlbnRzIDQgMCBSCi9SZXNvdXJjZXMgNSAwIFIKPj4KZW5kb2JqCjQgMCBvYmoKPDwKL0xlbmd0aCAyMgovRmlsdGVyIC9GbGF0ZURlY29kZQo%2BPgpzdHJlYW0NCnicK%2BTSd8svyjVUcMnnCuQCAB2lA6gNCmVuZHN0cmVhbQplbmRvYmoKNSAwIG9iago8PAovWE9iamVjdCA8PAovRm9ybTEgNiAwIFIKPj4KPj4KZW5kb2JqCjYgMCBvYmoKPDwKL0xlbmd0aCA2MDIzCi9UeXBlIC9YT2JqZWN0Ci9TdWJ0eXBlIC9Gb3JtCi9SZXNvdXJjZXMgPDwKL0V4dEdTdGF0ZSA3IDAgUgovRm9udCA4IDAgUgo%2BPgovQkJveCBbMC4wIDAuMCA1OTUuMCA4MzkuMF0KL0ZpbHRlciAvRmxhdGVEZWNvZGUKPj4Kc3RyZWFtDQp4nO1d34%2FdxnVmsUAe1n1oItsxahe4D%2FqRKPKIM8OfQRG0tiw58jbKaiWtA%2FvNddMG2gaGgfSp%2F3vP8MecIfnduXO9PFcoQFxp9%2FLsDM93Pg7PN0MOhz%2Bc%2F%2BA%2Fl%2FQ%2F39V1q4raFLubc9PWqrJ1w7a3gc1YZazVZMtnG1z6P8%2Bvz%2F%2B72%2FGw87HcDdyRt1VtqYpGt93%2BJhtBad754yff%2F%2B2%2Fvvv%2B5bPPdp9fUSH3ufo8NH%2F342D%2B8bvzx3%2F%2BUe%2F%2B%2FOO52T0n41%2FOdb77t3O9%2B5%2FD%2BK6CUDyqGwibbVWtapvXfSiTDS4tEMoBfJNQRlQ3EDbbtFHGNLZHP9ng0hKhxPH1oazZCvgUmMR5tXJji59pq3pbOriBx3XlEP2e93vrP2MO0t1OP9U721aqKfNiV9lWmaLQu%2B9u6O%2BfveIi7term%2FPHT%2FWu2b36j5%2BI%2BlfZ%2Fexl9po%2BX2YX9O1NdpbZ7En2NPsDWa5pq6KfT7LL7Bn9fJ09%2B%2FXu1V%2FOv3jloQPwZVWqUmuK1dbKlG0jBv7vmqPg6EaV9O0dwHG%2FUSMsKSWZuqzCls%2B2MldtU9iJ7iDbPsVZ1rqBe2cbECNo47q9xx%2BGWFc%2FX%2BPAr0CorGEoLG%2BDYgVskoqbChqEGegbColtSMiQTVCNU0GjMFn7UEhss5pOAfoShoRsgkqdChqFOUK9gSGxjcUDCQoKXTDMOOiV%2ByXvRMJTU%2FaqTpcObmALkIk06LPsc7qvz5IXyrStoUbQqJY6LRGdNbfrs7xHvZIvqTfyJ%2BqzPKVvZ9kfqe%2Fieigv6HOdfftt9vcJPYOibVTT1q7ZUnfLVLGOyi0R382%2BIHQX2TeE84yQOswfZip7GOsx5LvGumSp61Jpq023%2BZY3a%2BolmtKW%2FdAw3OgLTnsDk73cnBe6UrYtqqGs3wz2gxzNsKyeXmYoEQROoJMYggbbY%2BMTieuiMGdMrB7SDCWCsPogrneVL32s7GFGuUwinLmSi8nveb%2B3vQM2VTXlrtH2cCKh1nyLTPK77DHlkOdDHnlCP7%2F9FaW%2Fq24I94YM%2F5KQ%2BUxVEV4KqskbVVVWMlU%2FIFwvKPX9qUvZlJrPsnu06TJ1WpK2vfg0mvr9rYkN327L7fs%2BN7%2BfQmJJuUk3NGbUdPQrLSh43%2F6aFKTOmqyk32dEKR3z7sBrspbuy8PsbgJk6p3p2lLPIlc5DYXlAJvMOlQO7gPaKBm023hEcGkjPwJx2ahaV5VkQzXZ7%2Bkz9ihe0neH9jVZntP%2Fy6TWOsKlPGArY%2BTg3icSXVO4m33fN4BjDn9uVG6sKJl3ibLX3Xl%2FSXQeQR2lUZXrspYD96A7l47gq6qtqupa8Gi6LPmEOq7HMVVSEq9aweQ9MMUnr6WNkkyP3J%2BOQFqUyrrR7wkIHM9cJztX3TXL6%2BzrThxTqKUBTJ8ZpVPkP%2Bem%2BSzPc5vnpqX%2FBX2t6X9D%2F8t%2Bu%2Fsb2UwzfHd%2F00O5st82%2F%2BrK%2Fu6YwE6QSSOSf5b97MgDUWhV541Y2wkPhCPWDN%2Br%2FvuE8PEgmaMIF9aCIADbDqCPA3gCOaAOzItOSL%2Fqbioc2QbkNeE3Xb8kJaUxqBOoQk2c%2FfHY1FWVNAixhSBbObHltOFxLwSjOgy%2Fqr7n19If%2Fz0zR12HEUd%2Bl3p3jtInaVdhIL3U229008qBDAi831OaMhwpaXxEZ7F5JwA%2F2AdwpbG5LYu%2BB1GZto%2FyJrBpSg%2FV6tdEoc%2BqUSXlWbat7XThIHQqHeiw%2F70uVz%2BuZVWovC3KMFy2yYQLfdaNMpTNxY7r0kHoVDpQf1z3uJxfdw7uAvgrsMFdAG8ry5Zn9Ew2gtJ7LkMHPmxDXSTTlGEtbwt2i%2FwifHJXpRExARi%2BNL0MCd9Q8bZgLyh0RNHqYSLQCMzql6sDf%2FnSkYQb8TtqSwc38GCvHKLf835vommcvnTXJMI0XuaqaCstl94Cpz6%2FjTaxPI6cikc6Otjv9MBYz%2BaKhrCCV37f62aUXdIw73U3j8wN9y6zr6nP%2FUV3NSDpmn%2BjXP%2B1bHLKsFawIztep7zqJsFd%2B4tHz37SxSNb9KPUsqX%2BVF4Lkny374C7q%2BymuyBnfvo1OlPkqi2Kd0l331YuszfZV%2FT3q27I61pQ0rDXWNXad0163d3q0DQyutf98UHawJfGu2Vh7Tukvr%2Fp8YK2nlCrd1frn6edotaqOs%2BbU%2FHec77g%2FX72m%2BHuV6y1953asmxUW5UzIR5sYU%2FM1OPEnny2waW577WqeHuEE8%2Br9xGiRMh0g6BTuRDHPe%2F3duiublnUqqShseztFXd3tOras2%2FVVZfCXctOySGmNkobis5WSreSc5jcdf0LytFX3Z2Wp5Srz4a7Lk%2F2p4zh1CPqnaLTqEnXqimN8aa3bCqoS%2BOyoTvVwu9cdDaUnO%2Fr5tzYktJSUQR1vIl3CTwCXOuPIpd450hm033n0biLXiQZZVUFOL2JowExA2bWnxu6xDtHMg1wEc3NeVFrVeRNE%2BD0Jo4GxAyYWX%2BAvMQ7RzINcBHNLB%2F1OL2JowExA2bWn6u2xDtHwgGunK97d%2FnSj4AXcBCEZG%2FpUi4%2Bv%2Bf93vb2ovt7WWVe9w8enfhu63Cb27q7rra%2F0%2BruwJrPEm9majvcQioo7m6oLCiDJQ1PnnYjxOtuiPuiu5HUT0m%2B6CYhp97h1CTa7irsCWl3FJtxlsHnA9VPl7MN6J%2F%2FW9IMA6upoWt9kiOw7Igk3lGmPl1bVOWuaFpVmFL05u1Dag7uiTr3TN1vu%2Bfrxtvf%2FdDLzQh%2F3v31D4mDLlu3qiHYJ4F%2Fr4P3C%2Fr9MvtmaN%2FHtG1qzipvytM0CENsvhmeCXCN4qK7BvXmuMcApM9CNy3yHt88vZv9NfvfrsOfdOgL1eqcMrSm%2FoZuTz4f5iln5XHykU8Zs%2FSwBzi1B0ngblqCm5KQdM2rbGkwWlA3R2vhSRy2H9KZbg5UmTX%2B%2BsUM52xQVxI%2B1w3LjTC%2BgbQ%2BC6VIVl5Rf7ewu7qolCma%2BNO9t5uPfb97ZuayE9WXKWdykauqKOtdXenDj03fDtsvs3%2FMPsk%2Byj6gnx9kd%2Bj%2FL49CaAtlyiY2efO2CD%2Bhz%2FuE6yPC%2Bkn2IWFMQ%2BgmbbpZYaUqax07XW8L8GOC9372SVKjo%2FOB8syuqitFbS52PtwW1XAws3%2Bg3x%2FTzw%2F2T24J01zV9GcDna2qbfOY2t0W4R1i7Z8I4Z1E5qy7iuWOZ12IHs%2B03OsSSNE%2FdddIJ5D5sbyTfZhyCtTUubLUH6hIseLo9O0mHe6derbndkHRT%2BySBvbQCZXwjC6%2Bp0l0d0%2FPhLeMXXZ0%2FVuxG6mB09pd53Wzn0bb6reM5w5Cp%2BKRjg72O52eBuM1220ppG0ppG0ppG0ppLVnXm1LIW1LIW1LIR1%2Fvm5LIeEwt6WQtqWQtqWQtqWQ1j%2Bq21JI21JI21JIa4a0LYV0y0S4LYW0LYW0LYW0LYW0LYW0LYW0LYW0LYUUJXBbCulAYNtSSNtSSNtSSNtSSNtSSNtSSKsB3JZCWsvpthTSthTS6oFuSyFtSyGlJIJtKaRtKaRtKaRtKaRtKaRtKaRtKaRtKaQI9G0ppG0ppGO6QdtSSIuGvS2FtC2FtC2FtC2FtC2FtC2FtEp821JI21JI21JI0bHuthTSthTSfCi7LYW0LYW0LYW0LYW0LYW0LYW0LYW0LYW0LYW0LYW0ptP%2FX0sh9VdsteHb2%2BOlBG8Lrli1udJOJdx12vB7UFbkVkmAb3RcrX9B5QALMpeMkFOxCMcd7%2FW1rwdtLY2kraVMI3rrr6LPo0zPHn%2Fr75ksHjJLHEvRmEXV7p5xK%2F3cnntQ3Q37L7tB6YGZBP2J1%2Fjpgdo9k99UlTe9ZZOtx3UW8sl3Ljq7VTLf1825W2NIu3VxuI438S6BR4Br%2FVslS7wASXAjYR5M%2BGiyh%2BlNvAsQMiBm%2FRsJS7wASbhM2WiFRHjTp7nK3fPHri2E37mowAplUWhXyzbI5WPsM3wQHXAk1wZBfIxk1XycRhFo9lwetHFvYtSAZeBIrtmD%2BIQoTaNo%2FQUY%2FHmdS0WWx5Od0FoMS5di4Y073utr3%2Fit6k8iGpIXTS74fOxqT7kX1ijdNsWulJ5MFT7m3l%2FN729GENBvulspT7opYPwc7bNuWYnr7jocX1lPuq5eWBpu1O%2FyMAzzaequC5k0m6ak9u0mwEtj5gmD42zCAXwKSNuqwt0GOkFrqalJjI1h2Wquh7%2Fe7%2BbKuruf7hbMWTfH8Cx2fXuMwD0YLjx5MJz1mHSTzU0%2BNvUpyJ3fxuynX57BlSgWJyiIZbx25PqLBQ32tZug6SYZNSrPCza9ZVPZqGocPEw3uPC4xuPs7KYdGMq1VjUHL8jmt1uV5c7wgY9Bdl3LeYRu4lFFpkaHgbCNIwVMAL4gBbqqqVylT0HCR9nH2Yf4SvRqs9MWHAJuwslpc4L7RzJzbSaks433gg4EOmCQdtM9DZ6fhPY72c%2Bp%2FX0kSDuiEdETTpqbk9xP0jJFa0Ly2MZ7QQcDHTSRZV0RcARoOn1uGtZkZpOHyzbeCwof0SQSKgKOAIUjfG%2BGJ543FbkybpJCl6QnG1xYJKZDCK%2BW6ZjLx894DgOECVyJxAdQAzTrjvfTiEJJl2vEz2lGjuhG3mSWdAbAEaB1VzVNZAvlVq4RTyQMHXGOvAnnVhSqEL%2BpbKGEzjVQ8mYbQ0ecI2%2FCCR2FKsRvKlsTFRmxwOzlTUarpr8DmM82uLCUikQRAhXh8vEUxmGAMIErWRUBUTIaGRWJE4VUhGvEsxcjR3Qjb8IqgkIVojeVLaQiXCOevRg64hx5E1YRFKoQv6lsIRXhGkgx2MbQEefIm7CKoFCF%2BE1la6IiIxaYvbwpt2oYe%2FA3LialH1FsQD%2B4fDx5DTHMowNOZJUDxDdAkZGNOD9INrhGPF0NsBcUIz%2FCgoGClGA1lSSkFlwjnqUG3AuekR9hnUBBStCaShISCa6BBIFtA%2B4Fz8iPsDygICVoTSVpog0dkGVyGrdtU6i2LoaJJ%2BHGWFJKGPYAA5IwlNyfmALcIK65B1k9mIUV4JDRg33kICUYyu5PRQFaRO7CibAMzGOTIvMwQ0gAhrL7M1AAFzG8cCKc%2FeexSbF5mCGU94eyiwTP838YLmJ44UQ46c9jk2LzMEOTiYcjCpiBvKmqVFm0w4zUyQYXFsr7cYTL7B%2BUj6akIAwQJnAlKgMoSkYjogQHiAJ6ENSI5qcAOaIbeZMVBhiqEL2pbAGFCGpEM1YAHXGOvMlKBQxViN9UtoBmBDWQSrCNoSPOkTdZ8YChCvGbytZERUYsMHt5k1v50b06sBOOyQYXllKRKEKgIlw%2BnsI4DBAmcCWrIiBKRiOjInGikIpwjXj2YuSIbuRNWEVQqEL0prKFVIRrxLMXQ0ecI2%2FCKoJCFeI3lS2kIlwDKQbbGDriHHkTVhEUqhC%2FqWxNVGTEArOXN5lWVe5NtJ1wTDa4sJSKRBECFeHy8RTGYYAwgStZFQFRMhoZFYkThVSEa8SzFyNHdCNvwiqCQhWiN5UtpCJcI569GDriHHkTVhEUqhC%2FqWwhFeEaSDHYxtAR58ibsIqgUIX4TWVroiIjFpi9vEnTnuth9BF%2B56JSGhLFBzSEy8cTmI9iGSNwJKsgIEYPRkZA4iwhAeEa8cTlgQOqkS9h%2BUCBynCbShVSD64Rz1oeOeAb%2BRLWDhSoDLmpVCHp4BpIJtjmkQO%2BkS9h4UCBypCbStVENwYoKGWNFtMa1bhFrZxUTDa4rJRsROAB0fClY0kriABEuPQjqxmLAAMsMpoRIwkphi8fS1YBakQ0cCUsGMsopYhNYwrJhS8fy1QBbMQ2cCWsFssopZhNYwpphS8PZMGbAtiIbeBKWCqWUUoxm8ZUKBQeCcxU3lQX%2FTqDnVRMNriwkFbEES7VIigfTVlBGCBM4EpUMFCUjEZEMQ4QBTQjqBHNWQFyRDfyJisbMFQhelPZAsoR1IhmrgA64hx5kxUPGKoQv6lsAf0IaiC1YBtDR5wjb7ISAkMV4jeVrYmKjFhg9vKmslRt25S9cEw2uLCUikQRAhXh8vEUxmGAMIErWRUBUfZoxK5VHSAKqQjXiGcv5hHRjbwJqwgKVYjeVLaQinCNePZiLhHnyJuwiqBQhfhNZQupCNdAisE25hJxjrwJqwgKVYjfVLYmKjJigdnLm2ytytr2d8mnG1xYSkWiCIGKcHmUMdjGYYAwgStZFQFRMhoZFYkThVSEa6BkwTZGjuhG3oRVBIUqRG8qW0hFuAZKFmxj6Ihz5E1YRVCoQvymsoVUhGvMFWN6NYuhI86RN2EVQaEK8ZvK1kRFRiwwe3mTbpUuGt0Lx2SDC0upSBQhUBEuH09hHAYIE7iSVREQZY9GTETiPCER4Rrx5MU0IraRN2ERQaHKsJtKFtIQrhHPXUwlohx5E9YQFKoMvalkIQnhGkgu2MZUIsqRN2EJQaHK0JtK1kRBBigocXlLnqvamOHm%2BWTDl5WSjwg8IB6%2BNEgUbOIIQIRLP7LKsQyQscgoR4wkpBu%2BPEgPbGLUiGjgSlg0QJRCxKYxhSTDlweZgU0MG7ENXAnrBYhSiNk0ppBa%2BPJzYZhoBcNGbANXwlIBohRiNo2piVCMSGCmGk26Mcq09fCysXAjKCylFVGEQC24fDRlBWGAMIErWcFYRjmgkbt5HicKaQbXiOasgEdEN%2FImLBsgVCl6U9lCysE1opkr4BJxjrwJiwcIVYrfVLaQfnANpBb8TkDmEnGOvAlLCAhVit9UtkIV8Vhg9vIm9waB2vQPdkw3uLCQisQRLlUkKI8yBts4DBAmcCWqIihKRiOiIgeIAioS1EDJgm2MHNGNvMmqCAxViN5UtoCKBDVQsmAbQ0ecI2%2ByKgJDFeI3lS2gIkGNuWJMbp4H0BHnyJusisBQhfhNZWuiIiMWmL28yb3Fqs37ubvTDS4MX2VjW%2BVeoqTLXFVWH3il961eZFNlTXYvqzPTvWnsseALbQ6xBpSNy8fTKlMLqAeu8Fub6kKV7q1NJ2AdvrBqbbkDbDMrMgobP2BIYblGPLMzcnTYkbf4O6JOcJBP%2BI4oSLzQwU49dkjvuUZcZxg6agHI2%2FqvpAWgERghrY8zhbSeayBdZxtDR3wjb6tzi0AjMEI6H2dqovMjFphDvclUqs2b4ZrjZIML45clViQ5hMi95tWtaiOXjE6k8FG%2BgMJz%2BXhaZ1IB6cDVAYWX5%2FskCg%2FYZlZkFD5%2BwJDCc414Lmfk6LAjbwcUXv4gn1LhEfFCBzv12CGF5xpxdWHoqAUgb4IKj8IU4jaVKaTwXAOpOdsYOuIbeRNUeBSmELepTPVOV5S94GWeuVh0%2BaGXna7qbengBrawlUP0e97vbcyw%2FWfop5lCVVVrp9UGW3BqtLnSte77ZuH3oCyfCquG5fGNjlfv3x5kQaZ9IKdiEY473utr2jiC98pb1RbW7rQWfad8RZ9HmaZvddZkJf0%2Byx5Qz7vqXt5u3cvandGZSrdRJbx33tS5qltb79qWiNaF5Ivnq%2Bwie55ddi%2Be%2Fzp7jd8mH554Tauot9BNO25rVTVV5U1v2WRrVYzvjw%2B%2Fc1E%2B67rhwXxfJMxtpejgNUEdb%2BJdAo8A1%2BpqB%2FACJEF3aR5M0J4ZJi8m4XcBQgbErN9TWuIFSCZLIg1WSIQ3fZqr3FT9TM7wOxddPZQD0K6WbZDLx9hn%2BCA64EiuDYL4GMm6g8IkikCz5%2FKgjXsTowYsA0dyzR7EJ0RpGkWrd2D5vM6lIsvjyU6mcwJcioU37nivrz36XlT9SVQ1qmhyKyfviw6I65uQ4azbeJQ9dBt5Qo%2BkcFdW3LNgNJa1eS3Yo3qPoP2ePhfZU%2BqTvCCoL2nrzAH9hkxfkfGSOixf%2Bj88o%2B9vqPNyMXRiLsh%2Bub8TEwZVWFVSL%2BsdHobuV0VdR9eFNCldw5Lad1PKY647uJrA2Q5yPYJPAWlbVRSmPEVrqalJjI1h2Wquh7%2FeJ%2Btr%2BlzQ1uvur%2FRzGgmOwF1CEI3gLmF6nV11iK6Tmq1tlG5MfQpyHxC4NwTOnVfu3Lvozr2z4BT9cv8JCmLpP%2F8HQAXKvw0KZW5kc3RyZWFtCmVuZG9iago3IDAgb2JqCjw8Ci9nczEgOSAwIFIKPj4KZW5kb2JqCjggMCBvYmoKPDwKL0YxIDEwIDAgUgovRjIgMTEgMCBSCj4%2BCmVuZG9iago5IDAgb2JqCjw8Ci9UeXBlIC9FeHRHU3RhdGUKL0JNIC9Db21wYXRpYmxlCj4%2BCmVuZG9iagoxMCAwIG9iago8PAovVHlwZSAvRm9udAovQmFzZUZvbnQgL0FBQVdQRStTaW1IZWkKL1N1YnR5cGUgL1R5cGUwCi9FbmNvZGluZyAvSWRlbnRpdHktSAovRGVzY2VuZGFudEZvbnRzIFsxMiAwIFJdCi9Ub1VuaWNvZGUgMTMgMCBSCj4%2BCmVuZG9iagoxMSAwIG9iago8PAovVHlwZSAvRm9udAovQmFzZUZvbnQgL0FBQUtKSitBcmlhbC1Cb2xkTVQKL1N1YnR5cGUgL1R5cGUwCi9FbmNvZGluZyAvSWRlbnRpdHktSAovRGVzY2VuZGFudEZvbnRzIFsxNCAwIFJdCi9Ub1VuaWNvZGUgMTUgMCBSCj4%2BCmVuZG9iagoxMiAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvQ0lERm9udFR5cGUyCi9CYXNlRm9udCAvQUFBV1BFK1NpbUhlaQovQ0lEU3lzdGVtSW5mbyAxNiAwIFIKL0ZvbnREZXNjcmlwdG9yIDE3IDAgUgovVyBbMyBbNTAwXQogMTYgWzUwMCA1MDBdCiAxOSBbNTAwIDUwMCA1MDAgNTAwIDUwMCA1MDBdCiAyNiBbNTAwIDUwMCA1MDAgNTAwXQogMzYgWzUwMCA1MDAgNTAwXQo0MiBbNTAwIDUwMF0KIDQ2IFs1MDAgNTAwXQogNTAgWzUwMCA1MDBdCiA1MyBbNTAwIDUwMCA1MDAgNTAwXQogNjggWzUwMF0KNzAgWzUwMCA1MDAgNTAwXQogNzYgWzUwMF0KIDc4IFs1MDBdCiA4MSBbNTAwIDUwMF0KIDg1IFs1MDAgNTAwIDUwMF0KXQovQ0lEVG9HSURNYXAgMTggMCBSCj4%2BCmVuZG9iagoxMyAwIG9iago8PAovTGVuZ3RoIDMzNgovRmlsdGVyIC9GbGF0ZURlY29kZQo%2BPgpzdHJlYW0NCnicXZJba4QwEIXf8yvyuH1YNPGyLYiwV%2FChF7rtD3DNuAg1SnQf%2FPeNc9wtNCCHfM7JCTMJ9sWhsM0ogw%2FXVWcaZd1Y42jobq4ieaFrY4XS0jTVuOwES9WWvQi8%2BzwNI7WFrTuRZTL49D%2BH0U1ytTXdhZ5E8O4MucZe5ep7f%2Fb7863vf6glO8pQ5Lk0VAt%2F0mvZv5UtyYB968L4gmac1t7EJVzxNfUkNSwK96k6Q0NfVuRKeyWRhX7lMjv5lQuy5t9%2FoVL4LvWfIfKGh%2Bgwn6EKeacU4AEQJeqZJVoqt4AHwA1DHcOXssQKEJV6B7hUHgFPgDuGkcZhyItPgAkg0hPNMEbQIimC4iUWlWkEuAeEpC%2BASF8kRXqi7gkMj4BITzYsG05XuAtEa9%2BJueX31nL753fyGG51c87PlV8TD3QeZWPp8eD6rp9d%2FP0Chfqvow0KZW5kc3RyZWFtCmVuZG9iagoxNCAwIG9iago8PAovVHlwZSAvRm9udAovU3VidHlwZSAvQ0lERm9udFR5cGUyCi9CYXNlRm9udCAvQUFBS0pKK0FyaWFsLUJvbGRNVAovQ0lEU3lzdGVtSW5mbyAxOSAwIFIKL0ZvbnREZXNjcmlwdG9yIDIwIDAgUgovVyBbMCBbNzUwXQogMyBbMjc4XQogNiBbNTU2XQogMTEgWzMzMyAzMzNdCiAxOCBbMjc4XQoyMCBbNTU2IDU1NiA1NTZdCiAzNiBbNzIyIDcyMiA3MjIgNzIyIDY2NyA2MTEgNzc4IDcyMiAyNzhdCiA0NiBbNzIyIDYxMSA4MzMgNzIyIDc3OCA2NjddCiA1MyBbNzIyIDY2NyA2MTEgNzIyIDY2NyA5NDRdCiA2MiBbMzMzXQo2NCBbMzMzXQogNjggWzU1NiA2MTEgNTU2IDYxMSA1NTYgMzMzIDYxMSA2MTEgMjc4XQogNzggWzU1NiAyNzggODg5IDYxMSA2MTEgNjExXQogODUgWzM4OSA1NTYgMzMzIDYxMSA1NTZdCiA5MSBbNTU2IDU1Nl0KMTAwIFs3MjIgNjY3XQogMTExIFs1NTZdCiAxMjUgWzYxMV0KIDE0MSBbMzMzXQogMjE1IFszMzNdCjIyMCBbMzMzXQogNjUxIFsxMTE1XQpdCi9DSURUb0dJRE1hcCAyMSAwIFIKPj4KZW5kb2JqCjE1IDAgb2JqCjw8Ci9MZW5ndGggMzczCi9GaWx0ZXIgL0ZsYXRlRGVjb2RlCj4%2BCnN0cmVhbQ0KeJxdkk1ugzAQhfc%2BhZftIgIcDImEkAIkUhb9UWkPQGASIRWDHLLI7WvmualUS%2BhpPs%2FzjPEE5bE6mn6Wwbsd25pmee5NZ%2Bk63mxL8kSX3ohIya5vZx8JlnZoJhE4d32%2FzjQczXkUWSaDD7d5ne1dPu268UTPInizHdneXOTTV1m7uL5N0zcNZGYZijyXHZ2FO%2BmlmV6bgWTAvtWxcwn9fF85E6dwxud9IqlgidBPO3Z0nZqWbGMuJLLQrVxmB7dyQab7ty%2BUgu90%2FjOsneEhKswZJoAQtQYsAEvADcNIceRFHQBjQNjXEUMFqGCPPdwjBdXjguFaA%2B5YtAL0mRCNzDiED6IrQBSKUShBodj7UCiBXaOQ3rKkKKRxTQ17imsmONNLmQLC7qXcAh4AIXtkphUOgxw0ww0iL0XMsEo5gqiqBEQvXgpuSW24T4iK3L9eHvz3Yfnxlyl9jFZ7s9ZNFc8yj9MySL2hx7hP47S4%2BPsB%2FCDJgQ0KZW5kc3RyZWFtCmVuZG9iagoxNiAwIG9iago8PAovUmVnaXN0cnkgKEFkb2JlKQovT3JkZXJpbmcgKElkZW50aXR5KQovU3VwcGxlbWVudCAwCj4%2BCmVuZG9iagoxNyAwIG9iago8PAovVHlwZSAvRm9udERlc2NyaXB0b3IKL0ZvbnROYW1lIC9BQUFXUEUrU2ltSGVpCi9GbGFncyA0Ci9Gb250V2VpZ2h0IDQwMC4wCi9JdGFsaWNBbmdsZSAwLjAKL0ZvbnRCQm94IFstMTEuNzE4NzUgLTE1Ni4yNSA5OTYuMDkzNzUgODU5LjM3NV0KL0FzY2VudCA4NTkuMzc1Ci9EZXNjZW50IC0xNDAuNjI1Ci9DYXBIZWlnaHQgNjg3LjUKL1hIZWlnaHQgNDU3LjAzMTI1Ci9TdGVtViAxMzEuMDE1NjIKL0ZvbnRGaWxlMiAyMiAwIFIKL0NJRFNldCAyMyAwIFIKPj4KZW5kb2JqCjE4IDAgb2JqCjw8Ci9MZW5ndGggMTAwCi9GaWx0ZXIgL0ZsYXRlRGVjb2RlCj4%2BCnN0cmVhbQ0KeJztjtkOglAMRI%2BgbLJvIrKq%2BP%2BfaNPcFx7uB5BwkunMJE1aUC7YcHBlXrnh4RMQSou4E5Ps9lIycpMLSvWKWr2hlfmg40lvufMSDYxMps%2BiRdPKW%2F3Dl83658kx%2BP0BbJgC5g0KZW5kc3RyZWFtCmVuZG9iagoxOSAwIG9iago8PAovUmVnaXN0cnkgKEFkb2JlKQovT3JkZXJpbmcgKElkZW50aXR5KQovU3VwcGxlbWVudCAwCj4%2BCmVuZG9iagoyMCAwIG9iago8PAovVHlwZSAvRm9udERlc2NyaXB0b3IKL0ZvbnROYW1lIC9BQUFLSkorQXJpYWwtQm9sZE1UCi9GbGFncyA0Ci9Gb250V2VpZ2h0IDcwMC4wCi9JdGFsaWNBbmdsZSAwLjAKL0ZvbnRCQm94IFstNjI3LjkyOTcgLTM3Ni40NjQ4NCAyMDAwLjAgMTAxNy41NzgxXQovQXNjZW50IDkwNS4yNzM0NAovRGVzY2VudCAtMjExLjkxNDA2Ci9DYXBIZWlnaHQgNzE1LjgyMDMKL1hIZWlnaHQgNTE4LjU1NDcKL1N0ZW1WIDM0MS42MzA4NgovRm9udEZpbGUyIDI0IDAgUgovQ0lEU2V0IDI1IDAgUgo%2BPgplbmRvYmoKMjEgMCBvYmoKPDwKL0xlbmd0aCAxNDQKL0ZpbHRlciAvRmxhdGVEZWNvZGUKPj4Kc3RyZWFtDQp4nO2O1wrCAAxFj3vWUWdddda969b%2F%2Fy1jqQpS62NBeuCGm4SQCxYBqwZ5EiLMm4goSow430mQJEUahQxZcuRlplKgSIkyFemqaNSo06Bp37REuu3bdOjSo88AgyEjmY2ZMGXGnIV0S1as2bAVb7L7%2BL%2Fn4JDq6JL45LJ7cP6x94LLy109TOHj89%2Fc7pksB2QNCmVuZHN0cmVhbQplbmRvYmoKMjIgMCBvYmoKPDwKL0xlbmd0aCAyMjk5Ci9GaWx0ZXIgL0ZsYXRlRGVjb2RlCi9MZW5ndGgxIDQwOTIKPj4Kc3RyZWFtDQp4nO1XXWwcVxW%2BM7Pz%2Fz9z587szO567XV27diJ1f1NRIxC81PHTRWnaqAVSGAlaSI1SY3jnyTtw74g8QAiqkCqRJFAQjzgIqyEqHmogEp9oBJFPPLgikq8IMQDEuobzfLd2bXjqIB4h7s%2BnnvuPffcc853zsy9RCCEGKRPJFK9uLFWVaelv2HkbULEX768cuX64o%2Fe%2BDMh0jWM2VeWb66QMaKDfx28d%2BXa7ZdfeIX8Hvy3oKRw9fLypY8%2B0H4C0buY717FgPBIKID%2FDfjJq9fXbtG%2BeAj8X8G%2Fde3Vi8uE%2FKpKiPNN8D%2B8vnxrRfiT%2BHlC3Cnw1ZXVyyvyzrnfgl8Af2%2Fw%2F%2FY%2F3cgWEckKqDB4NPgjeIVoyF2LOMQjAaGEkYSkpEQqfOTfzkPiv9irgt8HyLoSfuLgL%2BQCaZEj5D3oO056ZJ40yTGyM%2FiIfIk8R0xUBac3MHMHFl4jr5Iqfil25DZwcgnZnpp99vkXT53MxsdfQhHwJmFGhFUCdpTQKw3%2BTj7GLhJRCYlrnZZai1ofv%2F%2B%2B9uCB%2FTv111ghEBm2bJLLkCE9SGyurl7w%2BDjFzAzJdsdn4lhmKGOiQfcauQ8LIxRlt9tkTFGUer3eZazbbaOjGHZSSzXJn5hwlNCIE1Nyi2F%2FjJZnj046fmlMngnM8UY1FVLsE8Ofs%2BQeLCc9Re10e62odsSmRc%2Bid20r9OO3SW4n9%2Fvr5BeQJz1s28U%2BitrodnvYFxaoWGYJ6ZQleVEoh1rRNzzflEpTC5cNN2mVqC06jhRHgiCZbGzmcMB1GvDuJnSWobPHza%2FXG51cNdcMxYrKddfrRlAIMk8MoygUk0wLlGgyM7XQceVKvfSUZPtClLmSLQZl24ilICnYWdVSkkYixDxmEmx%2FDT4axM6jCR9hblTrdNSl5867SRIemfXX1%2F1679w5blcBa1bJz3O7hmblFvHQKCr3l4ccZjWYOhHpAqKl%2BaJ%2F7rgsu2liyoLmxZVM7mSKHaWBopiisgTzVcEMQxa7ukiTYUxN2LWBfRD7Lgc5UtRa%2B9AXX5lz2fPPBv7Zs3nm8BjdQ1aNk2mOeI9jneOeh4fCkold%2BDkoXdYcgmM5liaojk%2B9QNND39HNsBw5MtWT1JBY6ktOxRX8snUiVlWfeY5csJOqLkeVwFJVJxQCWWHMFb0jQVC0RDePo4QcuI%2Fsz3gW9JQ9S6ii1hlPhnq9hr2rzCmOq5qfhNRTaCMNma0UCr6hhKZ0oFKIJsuhpnl%2BdvDpi75oWG7kCFoeExGVKJMzqIi8ZvKggM7EaMtslQ1lJMhwPFVEEDKNmlrrtWI8XkucWSdpUtUTPbnT%2Bdnl%2BXlelSbkN8g23hwx6pj0qFKD0iaLKI9go12voS6b3Xi3s1EtNtvFoq9bYck1K2XTK0R2GgfhkTLbSoPAl6TwmG117HnPnneGWOrYZxM2FXkdKbWJeqfdxRYAtF6bUCLKoLezKVSiZGI8ZWWBOY4Xhq4ZnKlXGzNzh8crjI03JstR5bG%2BVegbyz3cpzF6QmMHjqz6EmIbjVfSOGWO7XnUtt2xluQoGtc8kUXReONAKaRmuJt3MnRvD6PX4YWAgoCe%2BGiMP3lpaWthYVfuzq5cQ83lei31TnrYg2DWkS9Ex7cWF09wWQuya5CVR6i11q7G5%2BWtNzlePIc34IuZvz3ovujsc2WjnKaVSpqWk8C2w9C2gy%2FPTU%2FPzU1NzR1M04MHsyzH3h5haZBwhGSUY6nugVcqluN2sRQHQQx6PSmd2ColofO0k6%2FnPt3Cehtv8zx3or1U6O2quJVMpnEzqVDb8ENWsgJ5cXErTimTDjne5%2Bw9fNbgU2PPIzg9xKRdb9T3wOqOPIwZWysXuVmW5wZ%2BEpVpGseCY1uOGbOkE2elSFVcz7cMX9WZG6VjgRnYpm7renEfZnlVwGaO%2B1Q8deUHb74ZDrHi74lt4n%2FWnk7rZjHLirFn217cS0ql5PZt3%2FP828N48HWbqItDXDMM3801vOY7OT5qrZv7AnUNzKrKCLGbiSiq1PQYjZxIdy1TZGKRMlZIFNN2dbRIFlUZle66gSQqsmLpmhfSoKcYVSpKomioim7sz%2FfNvH7o%2Fmyv78v21TJj1SpjKRNcw6LUsr0mApVlY7Fn6fzz4PD3FNd1A7Hw8LXn2bivemJ8hPcl3Q2qeCYtleCFQqlvuWiWL7u6Vi6XK7q%2BeDNkLITiPHeMkY0hzgJPVHlUy7X1Grmx6qpYCpOxsSTKzoeO6Yii6JiGV5gJGDc1G2OBaU44pulAZxu4LuAb8MT7boGifS%2F96cowNi5k1j9bh%2BvxZExps9iQ51lj63qzzmUdyK7kNnJtQ1fzBUMMV6hl2FilhW4QyAuWk9HWSmRadDdu61j7H%2Bt0vTrEoMqoxSGw6Hxaraac6LDlNqew41Kui%2FTw9dw14VKY6pTqWbAs%2BdHRlbLnQtbL8dokB%2FZ2bf2LSmo%2FrqQbxTBJAZdlGTT2Y0ZjlKqNGKfBpOeHgaXpqmxIku9YYeDqqq6omljwuV1qvtdbuYdDBHkpdYYngiGKN2TbDNjEBJ2e1gCSrIkSDY55jYY3r5kFaYjJM4NH5EPybo6a2lAbvbj34XzbUYuzrQfvnLTVtPP9XI4QWfzO0jt3m191j31CTCE%2FKL57%2Bvwn%2FPnexfVrg08HfyADsgPWAgZDAf5%2FZ7BDJgUy%2BBT9wWj8cWsI%2FDTbR6%2BPSPdxXuzjHNGHX8Pn8BbYx1mmDwz6sHPIN0dPPqaPyByRNZKz982b%2B2hXnutug1yQMxpLQR5fD7ueGdk4OaJjoBdAPwbh%2FifgGyzgjig8RFGhL54E8XtnG%2FQiCHMS7pQFrCvgA1LASbkAXsY5SYYe%2BbsgxEs5C8JdVoFedRZ0CwQ9GvbU8AHT1kDboH8QouMOqmOdvsPvtnk0G8JXyFN70Z7jpythGW90tC%2Bkgi94iNWU4AkOmcLTHT0twSan8bRHvAM%2BGY7fPz21JDwUBt%2F4NilvN3E52O6XX3ooKKeu4l%2Ft5EOhwHsF3pPz3qmvocdZcY8VOStxVuSsxFmyN0s4K3CWcFaonRRmho2QfwJ3Au7nDQplbmRzdHJlYW0KZW5kb2JqCjIzIDAgb2JqCjw8Ci9MZW5ndGggMTMKL0ZpbHRlciAvRmxhdGVEZWNvZGUKPj4Kc3RyZWFtDQp4nPv%2FnyjQAAAwoidaDQplbmRzdHJlYW0KZW5kb2JqCjI0IDAgb2JqCjw8Ci9MZW5ndGggMTg1ODgKL0ZpbHRlciAvRmxhdGVEZWNvZGUKL0xlbmd0aDEgMjU5ODgKPj4Kc3RyZWFtDQp4nJR8CXhURbZwVd399nZ73xK6O510J2lCQtIhBAK5ISECEQirCdoSdnAjQTYdhajIqoIboOIQVxAcaRLAhGWIOq7z5ok7OjLmzaC4ZeQ5DDJKuv9TtzsIb%2Fu%2BvzunTlXdOrWcOnXqnKrbQRghJKNWxCD%2FnOVL%2Fb%2BMfOYzyHkRIWHG%2FOYFN4tfH85FSBQR4j5ZMOvWZmRGEkKGHiijLLjptvnjdJPyIH0Woer6hfNmzT3zzIg9CE1S4fmQhZBhWSUJkF4J6eyFNy9d6fvE2QHpHZC%2BcNPiObOQqG5BaPIjkL5486yVzWKYXY7QFMhD%2FuYl85r%2Fc9yXDkjHETK%2Fzx1GGRrsQhlsCGUglDzdD4lFydP0GcXkW4RwZgrSn3YY0Sc4F%2FtRB%2F4ZOdEF7MaD0VjEop9g5PtQH3oU2dBUtBVbUDZyoGloLGahTATdh59ILk9%2Bg0agh9DTyZfx3ck98HwzegNdgB78hcWoDE2A8tPQPPQN8yVqTD6ORLQO6dBwNBk70Cz0MXz%2FCX14GD2Cfo%2FvSF6AVm3obqivAlWhquQryYsoH93HbuFOSgfRg%2BgI5pNzkovQAJSFNpJI8uPkFyiEGtEz6EXoUwR3s2NQAN2I7kXbsZt5A2KPomdRAutJjKnmjkNLY9F0dAtagTaiPegdbMH13EnubPI3yTOIR1aUC31ahL7BpXg8eY7VJ0cmP0PXoi70FoyXfrvZa9ld3LWJyuSTyVeRHb2MZXwUv8IVcw%2F03ZV8KvkS0kN%2FBgNHJkA7s9E96BX0NvpP9CNZnVyNxqAp0PLrOBP7cQg4%2FjFxk1VkFfMBGgSjjUFvl6GdKA4zchgdQceAN39GPehLbMNePA7Pxg%2FiH4mezCXvMk8wB5gPWcy%2BAPwOohzg0VL0HDqE%2Fg39Cb2LOai%2FCNfjG%2FBivA0%2FiXtInHxPfmJF9h72F7aPCyV6Er8kJyT%2FiVzIg65Gt6PVwNtnUAc6gP4dfYR%2BRP9A57GCh%2BKF%2BCkcxz34eyKRLDKRNJOt5DnyO2YC8yDzClvKjmJvZP%2FEfsat5TYJs4TExecTDyd%2Bl3gv%2BXLyPZAdI9QfQrXA0btAKp5Dx9EHUPun6BT6K5UfqH84noGvh1ZuxevxI%2Fh3%2BHX8Hv4WRom0bxYZTmqg1cVkCfDpbvIweQRafxe%2BJ8hn5BT5jvyT4ZgsZgjTwjzFxJlO5gTzFauwIXYQO5idyM5gkzAzxdxV3BRuN7eXe5U7y1fwc%2Flm%2FmvhbmGN%2BG99%2BX1%2FSaDEwkQ80QGyK4Ik3Q6c%2BC16GuT%2BAMzBO8DRf4ce96BzMAseHMBh6Hc5rsV1eDy%2BBl%2BH5%2BG78Tr8EN6On8BP45dgBDAGIkDfI6SKTCGzyDyyhqwj95MD8D1M3iYfk5OkF3ruZIJMhBnMjGVmMNcyt8AYljKrmDXA2QeZPcy7zAfMGeZrphdmzckOYJext7OPsbvYA%2Bx73NXczfB9mjvOdXPvcRe5izzhPXwGX8jfwO%2Fm%2FyrwwhChXtggfCj8Q2zGGTgfeu5Hl32IG9bgALKH2NjVuBcyMjGLTDDyCMzDFFgV%2F0CVTALmxUifQ9%2FsxM1aKSWvsqBvyFJ8BJXi19FqnjCgJdke1I4%2FJz3sa2QE%2Bgg3YTe7i7mFe4cE0F7QRlvIUXIEj0IHSAWZTnYwCH%2BJd6MvQd5XokfwjfhWtBf34mH4TlyGV6MPiYOZgtegiuTThMUSHovPIugBuoudi65H%2F%2BcHl6PP0TeJ37IG9g7QT51oK8zoi%2BgL%2FAL6GXPJ70G7MaCNZoGWuQ%2Fk%2FV5EtV4M1tlqWI9u0CA38e%2BiA5gHDV%2FGj2RvR2fRv9A33GGQqFGgSc8kFrG%2FZf%2BWLEsWwAqDVYZ2w7pbiK6CFfMlSMkxSNPUdbDSZdAlxbCq69EMNBfdCVrvwWQ8uSN5T%2FK25GL0R6D9GQ%2FEP%2BM2WBGdQFGB3oLvZvQp3gTr8Kr%2Fe5z%2F2ycxF3Wjb7EL5%2BBiWA%2B93HJuC7eHO8D9nvsTPxi4vQY9ARL9V5BmGUYwB72HvkU%2FYRHmxo0Goij0dyj0vQHdRBqZY6gae1AzrNlc0OOj0iO5FWq5G7i3A9bzMVgbZ0FPXId%2Bj05igp0wojnQvgj11AGfZ0Lp52EG78EdkDMXtHY%2B%2Bg7GbcRDyVJoT4WatoLW6oY%2BfY6%2BAm4ntX4NBL1Qg6dDXT%2Bha9BcaGEIqsf7YQYOoXLQrDXMvwG%2Fs7GCRuEs%2FCzQNcEKNaJMVM79DRM0MDEhOZQsYo7BHpOE%2FDbYvbxoBG6BXphgHH3Ijiei0sRk6MMHmGHj%2BH2tF4%2BRecl1zIrETeiP6AWYE5VdLtQgpFZNVStHjqgYPqx8aFlptKR4cFHhoIKBkfy83HAoJzuYFfD7BmRmeD1ul9Nht1ktZsVkNOh1siQKPMcyBKOBo4O1Tf54qCnOhoJjxhTQdHAWZMy6LKMp7oes2ivLxP1NWjH%2FlSVVKDn%2Fv5RUUyXVSyWx4q9AFQUD%2FaOD%2FvifaoL%2BTjxjUgPE768JNvrjvVp8vBbfosUNEA8EgMA%2F2rWwxh%2FHTf7R8drlCzeObqqB6vbr5Opg9Ty5YCDaL%2BsgqoNY3Bls3o%2BdI7EWIc7Rw%2FYTJBqgU3FPsGZ03B2soT2IMzmjZ82N109qGF3jDQQaCwbGcfWc4Ow4Co6KmyJaEVStNRPnq%2BOC1ox%2FER0N2uTfP7B7432dCprdFNHPDc6ddV1DnJnVSNswR6Ddmrjz9tOuX5NQuaW6Yd3lT73MxtGuRX6a3LhxnT%2FePanh8qcBGjY2Qh1AS3JqmzbWQtP3ARPrpvihNXJvY0Mc3wtN%2BulI6KhS45sXHE1zmm7wx6XgqODCjTc0wdR4NsbR5NsC7R6P2pXsQZ7R%2Fo1TG4KBeKU32DirJmO%2FDW2cfFuHW%2FW7r3xSMHC%2FYk4xdr%2FRlI7oDZdH5l16psW04jRWN%2FkSZzHtUXAsCETcP8cPPWkIwpiG0mDeULRxzlAoBp9GDFTxuTAji%2BJSddNGZRjNp%2FRxLkcJ%2Bjf%2BE4EEBHu%2FvzJnVjqHz1H%2BiWiUysklUYPn%2FfF4JBLPz6ciIlTDnEIfR2rp0oKByzvJkGCz4gcE7EP1wNtZjcMKgf2BAJ3gTZ0qmg2JeOukhlTaj2Z725FaGGmMkyb6pLv%2FiX0afdLa%2F%2BQSeVMQJPkAoka8PS6GLv2ZFId19MJhcez4Px7PSz2vmxKsmzSjwT96Y1Oat3VTr0ilng%2B99Cwdi1urGxgvSceIl9GeglBed6kwTTTo42wO%2FPGaUM%2BNMyCUWgb218aVpjGpsFEOBP5Xmk5BvIyoM3mWUmnoV7J0L%2BPDIlemh1%2BRvqJ3%2Bo0M9JcNkbqpMzZulK94VgsKaOPG2qC%2FdmPTxlmdydbZQb8S3NhFdpFdG5tHN%2FVPaGfy8CZvvPa%2BRhjEQjwMhJWgUfuDeP2k%2FSpeP2VGQ5cCnsz6qQ3tBJPqplGN%2B7PhWUMXmCqqlksu5dKUn6ZQHQZBbyei9sjbBc5Uq%2FaU1TK09JxOjLQ8sT8PozmdJJWnaHnwKaAbJgM2H4G9GieTECdUGDj4QlxAow4QnOCFTlKpWhHHJhgkC2wCI7fIcwnCHMUhJIFx7EKuiHK%2Boq9ignKuYnxfBaqEuHIRgsFFAXPAnAMBBoPjop%2Fpvqhy6BfkZ7vBhEKW5Bn2Wu4D2Jl8eJa6TmQFyxh5jLFBbjDyLr0T2%2BwGB7ZZDA5iHaB3Eqtb8mBbpuQhViR6sY0RvcTq0zs5xWxwcIrR4OBNOr2TN2VIHk5hRS%2BnyJKHNwmilzdJHs9Yr2jzekWDwzHWqbc5nXqT0ajTybIg8GOhDrPPl5HBslwn2aHOJDa73eVCeCyxWiwDBmRmMoSIDqfT4%2FHKBr1eEpHNalUU00iDfpfzO8cug%2BryRA1qdihaacCbDTsNxDAhwHMcwSO90i7Pd%2BKuIq%2FqbfIy3gn%2Bp%2B%2Bg%2FIqd7jsN%2FKpQKiC%2BJBI5pyUhRfkHYaUWt5QXakXoty8dO9%2BfQbP6o%2Bu4QZE7lT%2BsG%2BSiyPRfPoOLcMwaLC0BCFhLmBIK9iBAgAlag0wQQ9bj6w9UnMWZE3smnhr%2Fdf3Glyv%2BkeiZ%2BMX4v0z8K94%2B%2FC%2FD8M2f4%2FApvDZxO4VTiU8%2FT8WYDYlPcZianujGxCSyEOZTQbWqMde0iyGihJGkIIt4DGchCWEIEXlElaV%2F6J%2Fws0UsYTvJ1g7zczdqHOntO9er9KLKSjog2mUcDJFSxTqkrIQQu83idJB5rzzWNmf6mu4NC0aUBhOTzuAfvwGXhPQcS7yXuObvzyZ2PzFfM4NRNfRF1foyVnWFSVheQBbI22CB7jYKkqgg%2BLMotFcIZFjr1QHxH9wTetofyw3VtD%2B9faev7I51JFMaJUyJw2K3CYQZPaVmWMb8Dce37RpV92JiUvvvL3yx7O%2F4BVz4SWLAhfd%2BSJxL%2FKL1JdmXPEOGQ18YNFTNBCaMJYyNEGoVwYrC3xEPx3yH3OzDN7kisITG905Qzo%2BHdkEE0rM6uEjAJZjBN36QeNDNff%2BzLTXGp2GZhrhuYOx0VbqR%2FIZsgkrZTpzXMZPDIMfXvyxKHEZ6CR3BDbDeMImpBg6xPtbPxlmWdcuH8S6wv7VmYxXj6RrWmj0X6y0fXIRigYCZF0qHZJeVMKHEmcffuwWTotNscMvoZPbba1N9KAHfRw99yMSV6syDrkOeLu877JuuE64T7hMesdpbnVGdOd39BPuoaw%2F7fIbIe%2Fwoly%2FzjGGrXdXuao%2BY7cp2Z3sYR4idzq537fDuyNiRuSdjT6ZoQZlKpj9zcObyzDWZWzI%2FzhQzO5PdqsNmj2YSRW%2FKVJAfET8qAiOaQfCow%2BKIok7yVAfBelMnnq4GffpCPdGrkK9%2F3spJJx0OMHox8vhMJ5UVxD3gg1dTDD9HOd5SUTGeznZfpOU0KLBIrKXCbCnH5pJIDLacLpSZ7G43l9M%2BtJs0pBqVclZUyjnRDNhcHtE%2Bjft5Uj21QdVJXreXeK2Yeo1QEfzFGqkQ1U1qOIa8YBJlAGQme4YOHdqIW2KxGDYHhljKhpQNKY2Gglm8kDMku6QYrGle4FleYPUXw0rb97%2BPDJvX2LBQTHztxuIbn164anxJ4vxVDswlfnkES3%2FeX3nNtOvn3fCbjK%2Ff%2BfalOR2zq87Vh1LzNB50rRfmKQ99qhavs79tJ7%2FJ2JRBnmde4HbZDjGHuUO2z1yn3KLDhu933O8kAdmAWOy0OgI%2Bg6KXO3G2qp9owKphM6g2A3Z0YqKafNZCK7FSBluf93IYmH5QAdkCGQT2FEM2%2B3zYENd3wyzoHcrJ1b7Nvp2%2Bfb7jPs7XI5ycmI2zPRHHSecKfBK58%2Ftnozc1HYBjvebywlh6SmhAky29mLKzPM1UyldgKzAQxaw5DkdJcYp%2FQpnjEiNHkpJi6pcIDghQMCt7PFYMSyZds2LJ5CF1viUrG8aOma9L9Hlvfu22d%2B9c8MGqbYmv3n8z8TO%2BN7DwljXNN9xh%2F5JZdM24hrlNA%2B%2Fdee2am9a%2Fcqv36L2vJM5%2BCWuK7pfHucOwW8q4qgsJyZOqVFYe5XMhECgbpNzSKK9CAKmTan0gDM8gyEP5bD6XKxfqh6IyrlJ%2FA7qBzGPmcwvFBfLXjGkcj6nyZGRJYgUJg7MswKIXeIll%2FRxv4zhelFVP5kiZNqHzZEblHMIwPCt14qOqkRcIx7IYiXrYsWBNzFJ1PqwdibWCFukk2arkk3CR1CoR6TDJRiyUkPygM9y66%2BektMH4Pvf5WMu5WIurb8LoeTVf9W9K43vNdE%2Fqi0Q05bTuTm3LASQoFRXr%2FvCHlPgfkKKSIYoiVOLr4ropdfEBYG51ISaZaBdZ%2BXAyAZy6uJ9nhw5Ny39q9QQCDHxxwMow3PHE71v7Dt2WeIMMx%2BX577yBxyc6uMMXNxJ%2FX0%2FKctkKvJ8NvLeCJhiITqqVK%2FLxQuPK%2FK%2FY8ywrBewSnzswkOOw%2BOwT7aTIvs9O7HZbMCvHYhX9thyMiDfczLfyhK%2FLDe%2FTYz1VIpIuCnvAfWqgaJA6qH5Q06DmQa2DtgxqGyT6BxUNIoNsWX7ktxaB2HeSTR0Fg6f0K88%2BUB%2BxlvORlOBqNhAFTWQ1BWJPtrZnltupAvFQ1LrfSnVGIxSiEk3l%2BRK3TMCt%2FbIfOEPFOlA8gFA9QMWXB3XABUAtFZcNoQIeDgUZcyCdCAW3knEv7V03Y%2FHMtVtiTy0fl%2FgyYcC5r%2F4u%2F%2Bpr6sYNfG8PtrRFRk1Rb3uHO5x53WMzF7wYCR9dPfdYi0Ek7BuJ33HSNVfVTJO4vq7ESkkfmzDqunyqO2Ylz3DXw%2F7lQR%2BrE9ZKG2wbHDvRdv5N6UPmQ90%2FGSlHytXnGvJseY5l3DJpLScKVsHptDqdeSSfyeGEXO4xbpv0NvO6jqvEEzHBkxWEe9BZWD6U5WZXVMMySAz456rTVcCKRtVoiRrrZprwRBM2qXZXFHR6rpplKZAZ0w%2FG6egHpFXlKcrAGfZwm4BNgk8oEhgwV%2B%2Fr8K5Kz0sL3U1j52NUq4AiOQdq%2FXSEYhqJ0T0OU9XL8WzQj8wKCvidDicXonrDrFBlwlZi36jEn75PfJ5Yj2%2FHUWzYPbc48WfPc8uf%2BeNbbcv3EO%2B1Z7%2FBm%2FEMfAt%2BdOf18dola75N%2FJz49vut2jEjegRkdBbIqIJ8aLVakgtL%2FirnPHaenst3ljvHOBodCx1cuXOId533MW6rjvOZqWBaLTkmRXSH9wlYSEslHZdqbQ1gf6AoQAJmC8ihUqQQhcqh%2F3%2BUw0tCSMfZgqkgOR2a8cLTbzAlRiMJlRyQo0dI5stNd3U2FZTNH3%2FP7Gf7PsC5p%2B4oGzOzouKmKSMPcoczQq8mzvz7wXva5tTl%2B9hXL5YaLdNf37Pn0HyLMbXHPAq2wFkYqw5tUUeIHCuIObzFx%2BEibh9HOE5i2Bzwc2QpR4dEga9jyBgZ6bDO4zcUGVQDY2AlP6YbOogFjEl%2F%2BZi0SawYf67iXMX%2FsLQ4WFOZ5RysKVha3BVLi%2BFALw0uKjEH7IE0PMpWXvyG9PT5mRLu8IXEkZ8SLT9p%2Fd8G%2FV8D%2FZfQErUS%2Bs9zOYJfLBKPi1%2BIbKG4RSSiiFKDkGAElfxE0B2TqddEPH5dkY7orhyB%2FD%2BNIJYysfqoaQ82xv%2FQw21Mb99wMrdvB%2B3dcxf6HqR9mw1r8BisQT%2FoudqhA%2BoGTBeWi8v194pr9Pc613gl3sl7LU6LN9ec68r15A4Qx%2BiuZadKM3Q3sL9hb3ct9RwyHlLeNLyhfKKcUYxMBu%2Bna071ecp9UDsMCTsyCnjJQpedpW6iFVvpmrPSNZfvKDAxYLb63TMhO2yZTnx%2BPwNDzirKIlnucJuMTbJPLpIZma69wKqdV6w9OnjlXG%2BLtm%2Bk1iAsQbq1V%2FS1RCo0tactQ1wK5iYL2zMIJBhCJX42vRLtigVWYlkpU0lWxRI7D36V2PNid9f972MzLhmY%2BMy3t%2FXVL78%2BGjtSTbw%2F9XXO2PAKXvDBl3juzLFfvlN2053nf0z8kvhlbPSwNsd0z8jXZPQZNUdiOZkhkpzDWvYxmGGQ5rERQRRBQjnRz79L1x%2FZpGaphnpDk4FpNrQaCBXXNkO3gTUQXWq6u6kFqonssiuX4ZLzsbRHrG2eKYcuZU8ymswyqe2Aov8is%2F1Ccem7FeeSGpybONl3lDvcd5xU%2FVxL7upbDWO6DwZ2AMbEoMXaWugojkY5qjaCORpWK23OKOJUrp5r5Xo4zsc1cc3cWY5t5UCDEgaJhPkUPP846kFMN9XLdFAnIMWiW9jB%2FdO5JD2USs0ZagGfNUL7dx%2FO5Q7%2FXAv9eAx4%2BxrlLX5Q9Yg8tlhkmWMIw8KmJcmSKHOSKMliJ35ZjQi8DfxuhpozMpgzsiyB%2BSIzEiPqoDRYL9AxpNOJgggu4tx2bowISLUImrIglzjfryrm%2FMp3NxU6V0rhX2K7G%2FgOu6yzHAGA4eKKsNRbphGRRkSlQvwDQ8OKlAVzUPLrDFGYl3faxTBYMtSUQdUNqjvEh6Ut7Ha%2BDVyoblZYw%2B9mv2bPc2B5JXs6yiZHJcrwbIjk8CPkpcxa5jHmMelxeQ9zmHmbkV9hTjAXZWaEPIohS8DqwZGWWKMmD3zy6w6LrpLvTH6tWk26SrbI4IBAb6tk%2FTpLJfTkRIfJncJGZwpDCQ1DIQ2ny7UbrZUoZVDhlFOCY3SmwFOGP8H8GEjSdPxA30lSm7grcTOo7L5lZFPf6xfvIvF%2FJkZr6%2BRJ0IXPcS8hDo1QPfUClRMW9nIkspwHfN%2FLuc8P7rpczSWojIzvS4uJJsH2J6HFHu6lX8b%2BROsGhca7QU70xKXqdExIDOkYFtYfKG5VyhgWlf3Dhkc1fqax%2BmzGIMiFgAcp%2Bpv0vQz2nSxbSQarSD45SAayfqkQXPyF7DzpBnkFWck%2BK%2B2RD0qH5fPSz7JjJ7tF2im%2FIb0tf0JOsh9Ln8pnyNfsl9K3smGFtFK%2Bh9zH3iPdJ28hQoNuHrmBXSAtlJeT21ihhtSxNVKdfI14jdQgCy650Bglw9ioNFyuNAoM0bO8JMl24mGdkpD2%2FHzAKFni9IJQzBv1xWCoKgwR60VDVEcDbZRGkC1RNYajOhpA1g5VoRGdyGBwu4ggI5FKb2UFldnUVMZwYa%2FyYS%2FN8HYmh6sF0IqfFSWpmGFtDMMSnSwXMwSiBKph9CwhelhWkiD6jNjYiQ0d9C7mMBmqqYhrYynV4JwyNcoVC6qwWsTisdUwC8d0fp2edJKhqgV0ggoFkQqFULGPmshQjYFqOeVcS28kolT8XanwuJW%2Blr6WCo9LAbcAMpTTLSh1fFVZkVpul%2FkJaZ%2FAOgVkXkz27Nf5qQMQ0z6aTokgWBMgNiCrKZk1P4iPYBkL%2BGiiN3Eq8bfEX8ANcDFf%2F1zL3v3LKgogU9tB9wTp3o3%2FXTVKDC%2B6GafIWkCzAXcRXVlUx9JhU6zmw4iYYkEEJSQyIiECIwG%2FgFcMS0fM0hGzxfy72jnKJtWt6up1TTqmWdeqI226bh1J7feilK5UW%2FXGKVOiUvEVO4J82Y4ALhPsCf2bAqQ0XUptf9BK5eXrBtHBA4dSckR3iB5VAqkQ%2FSkZ6X5ZolKjOVYRTSFVa6VaD%2BlKxVZdqTawEZ5BUXEKBBzjYIoZlWFrmXvBdGkT28XTDP8H5l3xM5HxM4VilBkuThQfYnaKbcw%2BMc4cF3Uph7WkNErUEs1h7VENhcVR4qeBYCuFnG2qFBgUJVMh0ErXDvBDCgKRCIKLME5hIAkLw0mJMIGownVkuiDZiFcYT0YLjwt7hT%2BST8nX5IzwL6ILk1xhnLBSWC%2B8SHi6nyyJ9H9Qvyg0Ik0SqA7B5u3YTxqwNfFJ334QgALmg59rmaMXa1J2diPYR2fAPjIhL3panbaN2yZu1283siIWjKJJcIVdK6UVFmGFeaV9LbtB3KBfa7zXssG23r7eud611qMXLCALHrvFY%2FO47B7BWmCQ3AUC4wjvkzGSFdmfsm5Uf1GmmtmU2ZzZmtmWyfszz2aSTCXchrAJDPwibdbv68hY9dolE0izxmOaNd5b2UtVYqwFfLooeGzUxkk5HQjbLP0nF3xjdfHvFmzowDX43sSqxLFEV2IVHvzV%2Fv1%2FO%2FXyyz3kw57tze2RYYlbEo8nnkwsBtdj4b8SyWTy4oVfKB%2BoDX4B1gHlwwo1h%2Be6bF0u5ioOL%2BA%2B5ojFnGMwGpFXoTasCYmO%2F%2BZhOHyZRenxcZmK6XI9n3Glk3HJx0gbs7%2F6GTBl4EalndVg0E1gaGlf9VH8Z2ycvGrP7G0Tbnj7laf3La%2B%2BfkxpG3fYETi1b13nIrO97xP21UTToNlV9QsNMjRM7TWwd5AdBdAF9e5y01jTNcINuhv0e6RdxrbgIeNJSeZFXnaKDnmIsdZYaxJERTLbjDaTTRliHGK6yrTMeJvygaxbKa10L89cL613r83kJYdN0puMU4zLjGuMjxifMXJGv0FvMxj0Jr3d4HTkWBUbbrK12YjNhvwByi5gnB2JRnrAEkYGBYyQD73hNj7Od%2FMneJZf1xzE%2FmBRkAQD9su5lnW5baLJQvp8QFOPv7oBmh4AHRAzgj2CzenzADCMW2KUocUaPwWHw2kNMINIMGg2%2F8pVcPsXf%2FdR66uvNN15Q0fitx8vmXr9%2FIo%2Ff3RDxcQx2QfOcIcnvnP3c59kDF27N%2FFXXLm3MdC3g5mQ3TBq3LV6ju7H45JfsT%2FC2hmIT6gjusydmYdy3xjIghNvByfe7orM4%2BblLuVXGpbmfqr%2FOKhvlKcZp2U1Bhfq51sWBBblLhi4InNt5taA3hKke%2FYAX5RidZ7bE52UNSn4StYrQbYlqyV4V9Zdwf%2FI%2Bo8gH5HzDdlZ2cFyQzRYJ9cZarKqgzcY5gVvM9yetcGwMet5eZdhd5YVTEYDn8UH3bLb4MgSsoKygcXO6S7V7Y8uduHFrp0u4jpM5iEv6CE9ODJe7C2wMWgMpopprMcfLcIqrsdNeAtuw3HcjUX8d1b1lCssZgvyJdcPSSd2qlZn1FknhEOeQb5wmxIHj7oO%2F2BOTaC74P20zNdNadiP1KGN2umOdkB%2FLrKEHim0RM7FIqdTeEnkNOx3KeWlGXVZwA9v5sggNc5S%2BG%2Ft1vIsYA8gSL3dbqGpE6rJUm7wW8plDUw072vVqIc8Q7nsomAtj1z%2B6T9mtg%2BThxlKs0qBj2MN1Vm1weflF7JkRI%2BaUy7%2FpSPRsPYtjQ751bESeLvN6WA1yaLnH%2BOw37Nz3eYHR1wd7fp707rVP7yAbdgpJE5a77zzrrGFA4fi%2BLvL7kui44lvEx%2FjUxkPrr9tUnSs1zJo%2BPTbXmp%2Bbf6P7xha5pRmlUdzCufffGzTqs9vxJjK10DQSV3aGekSNVgoFbFFXL3ULLVKWySBxxzJYRkiIFFyOj3sarrj4gJV5gU%2FLkKr6SqCpJkx1pNm0kq2EJa4xb4X07MyqWE%2FgVnR%2FOq%2BCghGz6s5ndZJFZoBCltHKfWq8ReJ8ez9iQnsqxcu%2FDJSs3Efhj0jG%2FrlRhvVoYIoSIICakS6SrxKEq6RpitblW3m7fYnHLuUlx2f2L%2Fkz%2FM6g16PERFyrJJe5ze8Sw0rzT301tPLvWZvq5f4vUXeNm%2B3l%2FVi8KP87iJ3t5txU1Xg%2BV%2Fdw15NHWhulTVghklxaIsb9j3FSIJZ9Him9GGcq7NuvmNVqwfnFt118qX3P11ly4SN8KtjQ2fcvGDrS0zkYiJx4bOtjbOemLbqPNJudBE3FcbHY2MHYrBIz0cs5ZoBOtUzLNotfow%2FJp%2Byn3IcNXZXctvwVvIYu53bKYoM0vGFIjWom8QVWHAjB5%2BHQvxYdBV%2FDcwjQ4gfIxtMb8qF006kmU4yW9Xx4DmD9wYKkztMZiEWliIIt47Fq9lW9gu2h2XZTqxT5dVMK%2FMF0wOGP6zWg1ACTM%2FDWIcIPYsuwhi7hcvOomGNxc7FYhFX7yXLsvdKu%2FJXq6m7Q0nZSwfBVJoKBnVMM5a0SxYEHhGKBVKeENH1ncNV%2BFa8AA%2Fr%2Bwd3%2BJfX2BHgyGqyISAkbKI%2BCk6qlggT4f26Eh0LrNSpwDrw0lo7ADOX4XZ3KViBZ1SJnsa7IdD3pxBNcVQnNjoyo6wfAgHcBl7vQXYpD%2BVIwjfyGf1P0r%2Fkn%2FTcm9zb8pv6z9CH4KV8rP8WfSlJe9lnuL3yc%2FojbAd3RD6of4uVBrFZXKHs1z%2FBPsw9IT%2BqF9Pn7iI2Gni6nxsDKUNXggg4GQHa5R0dKf9jh2qn3shcmtLxDMICq51oaevmMo9D25K8B17VsZy%2FM1nUwYPD0ZksVq9jkN5%2FmQzIPMcV62SbTidLvCD4RckmihKr0%2BvTrgk0wugRwaye4WSdIIm8KAhcWkw0JwU2Vlj9heCDdOIiVfbzx3TH1ELqE0JS76dXFwS7Df0S4XGP74t5XH19HndfzNV%2FQaFcujanX6338GfWQmSmjsj4yyXmSpSyqDVHpCVthdKghQqLFYTFqgkNnpd4GheewnrYVfB%2F4PzEjsQbic8Tp2AdmpkfLiIWgVcy5pdOTYYeSswhazS9V68W80WcyhGuDDGV7ESWsGVYQRaG8ERgOQWMsHPcEwjRcZ7ADG4SF6ReEqhQentjdDTaewGVfZABMLjICk510Fxifwj%2FeOZMYo5wzamf55yibYagTZfWZo2aX8lN1JosYlWtRQtSBJ4nPuDrOcyxT6AvYERN0jPLU231aZWnWoJWe%2BnFGmjPEnOwlLgSxjNgSVx44RS388%2BglMcmv2YHsSNREBXjFnWh4BEzuEyHZ5x3TMbYnD8rX5ilIe5a9zWh%2Be4FobWhh9wPe573dHnf9Lzl1fO8we7g3Y4wn2dvdK8ga8nz%2FEH%2BDV5%2FPPqpQjKziwebBxqy1cigaLaalQuBOzO6OPtiNsmu1e5di4ym6IhMTO%2BH45n%2FymQzMwfiEqRCLrXBCZoWUDPMlQHVq0Dg8kQDnWTpQVbQG%2BSBdGXAMw3DYw1DiYFQQlVtugGDQ2KelGto9Ol36gn4u0lweVWjI6r3TIziaBPw9wGqmkryAjOd%2BAsnnuic6VzsZJzukkVV%2FadkYBm09MboIVQklTqtaXiQJVhe4PZp9oJm9UVSi7a9MBO3NPb2K7BscPS8mdGp2XOzSSzSSG%2FGQJIZo5La0FpidGMPwzZOTULG5nAG6M7O87BN0N29bEhZyo3A1Pq22xz0pqhsSCmel4y8%2F%2B7RzjrGm5P4VqcIzJhnY88em%2F7EQ69fXb%2B4biq%2Bfsi32WUNNVePLlF05K%2BDHn%2BkccPLic777r06o8wt1ta2r59xf11Gjj9j0ujhifctxa5wxfDpxaGy7Hma%2F7UO5OERze%2FIQE92IUvygjpYV17mvcpLLNP56fJ0x3RXY8ZPAl%2FKDjcMt5Z6R7N1hjrraO8jwmOSrDfC8kYemIZ2TrDR2bDqdCYkOwOip3kAHqDkESZk6sR5qh43o1ZqmWVWpjjeUjG%2Bt6%2Fiqwngj6S8kV66m4L50xLDseoGVTefny%2FPd8x3LcrgYuBPameWwDwLOF7AsrDdCtvtJd9rHXbf3f5qItHXde1%2B1RIde1vsnjUL5q3lDvedfSRxJvGvxNnEZ9c27iD5z01s3rn30FNP0jU3DcZeCWvBjf5DndRgarQ0OhaaFlkWOe503ebeRrbp31DecH2ifOz6hv9G%2FMb6jf0Cbx1qHWofZxnnqHU16hfphWGWMkeZi1nBrTCt49aaNrh3W3Y5uiyHHJJRk1Fv1Khtk7aoscRAc9wDoho2maOGw5hFMvDMYtYhFYoiFcqhki0gqYdBPbPwyO8UMM3FAVRooBFDYCIYLx6vELC5PQ1Vv755EhvfGznXG6EH9bHTkdRdGeCUPQk8TR%2FJU7kaUsZRsaMuLAgjOzjxnXHOxEV3rr6xfr4d2yLn%2FvRN4jvs6H31S%2FJ98ZSpD%2B45tuPaxYW%2FfxWHMIsFnLOLys1U4N2stNxsUQssjXyj3GhJSct2EI0LktQ8oHUAGcZE9cPsUfc4pkY%2Fzl7jfkySbJq46KjUqEadYDTBVMjOPKMhhKmkmEzIs5nKTkB0ZzZUXBphy%2FmUxGi7Xcon17wskBXDIn6RvMiSkhY%2B1hgIlKYHCN650xzAl4sKOyvxS9X%2BGS8nfkm82n43dvdZCmtun7V%2BzYK563Zc24jD4G0YsfsRolxs3nP1Lc89%2B%2FJTO2G8VTDeMMiKDWXgZ7qQAuukVlf%2BmPS4Yauym9slH5GOGDo9omjDY8hVfK08ccBuwyH%2BkOdN%2BS39x%2FJJ%2FQXhJ4Mhw5RhV0FH2FWjOWqyH7e%2Fa2fsmjQMqNSw0QmY3K%2BCi2upNzYZidFloV7RIbc3ikss2nVrpj917ZqVl8KRghR2ZWhYNYFCbaNvhyrQ7ZkWC7C5g9VZXJTd2ToBBXChPSVEhQNmDlg8YOcAdoApIKoGUxQYntaHkSvuX3vBKVJtLjXXVulSB5ggACXsotpa82kq%2BzSnyQKdgBIW2hkoZEkra4rb%2B4ueS2%2FSGgGCB5Zy2ul2J0XxDkkeqSWrApXaNt54murQmNa8UQUuGWmjRtq8UQVmpQ7UtNcawHUD06FEs8VBW2Aq4n4wv6mMIyagWebWlPfkJD9j15Bv9iW%2Bu3cRtn3Qiy18n8rcPWvUjDCzcvp1FRUYTy58%2FKmDD54CWYgk3kwcu3PTGHzT7aurq2%2BlesMFC%2BAr8LsdqFMtHsLifNav%2BM2NbKuLE9njLmJ3mInN4jAbrSakGK0YKcQmiSYdnqlL6oiOToTMY7PJgZMO7KDJAQrUexaq5q02WSqpFCeK9SIj5iqF5plmYu7ErGowWkPENhO1ObodxEFlQtJHHW7nyi6yKPUmWARUKn1782IMHCr3aeSCZUKPKADAAGkpL06%2FUUh3ImuJ5lEWOwVNK9hLwCoJmIOuHeWPLVt5a6h65IjS999PnNnBhurXrpmS%2FQelfFLdqYsvM2O1tZ%2BYxDZpNkQhnqDOXpG5LpNY9IbmwWsNrYNZPw6SIFOES0gJo%2BJqUs1ca2q0NeZMz5sOU3Wj6YL5gtUy3FDiGJ5bMrDOUOOoy60ZeFbf55QfgF1bpzfo8vWGsNHhtBcY9ODmurLpCjiorQBN0I1mTUg6dPoUzs1PLYBgTgoPjqYWgmT3alv%2FTI4qHJ8pTJFRLqAM19kFl5vPz9OFPC6qdCS32%2BPZPBgPBhXUqcqoJDtgcRdd0j7n0vpH6VX6TvdvVn3n0ief%2FRYA0jqnNd4Ok6OJb%2Bryipq0FARR6d%2FiWjS9ZVpkW5SzIG9%2BZFEhT3c5J%2Bdw9u%2F8paDC0gLsLAXvEjxKP5gKVtuvuuw2XCVm5k6%2FpSzHaljV%2FfGdszE%2B%2FnorFkY2H9mc%2BPGvF%2B9pWvDA%2BoXz7qkND7UPCDgGB69%2F4sWDmz%2FCOuz53aMXrzp6%2BIaKrgeM5J4Xnnzqt8%2B1PUltX%2FD5G0GvO1C7GjFhHy6nE6mMwqPMf8H%2FwpLAObhs0mBeaOYwJlab2WJlbASbKFMzGUGSZZtddiCkk0OipPqzo%2FsknJSwBGymbwFmZUe3uNpcpNl11kV%2BcGEXsoUcdk1tQdk2Oz5rx3a3szLF%2BJYlkfQVPMTOp1Mpbwc8hl7gqVMzsETNBYfdgBoIA4gdRDmqbXc8jeK964%2FN2jExM3HGP2lE7S0lCbCH%2B77cOaZ5%2Fea%2BB8ngXTNKazas7fseBg2y%2FTAsxBe1O1kBrehCEr2FNcuVqlQvkVYpLnVLJ6QfJM4nNUmrpTbI4BheQBzLwC6manevDIqBTcRzvMDKRIA9U5PFQHaUdYvpcf06jkptef56cQyLc0nESjsN8DB2J85gN3sIs4mLv4xjQ798pvknG2COZmp34f%2BgfvOpDoNZuyVR73QXRAVGYax8WJrP75OPy29Jf5Q%2Fk%2BUpTBNDDIJLquWvEZfz3CHpC7aXvcj%2Bk%2BcmCBPE%2Bfyd7H3sE%2BwO7nH%2BceFxUfaxFj7CRrh8Pl%2FIFwsNdWwdJ1%2B6DZYlhmd1HMvT187pXa%2FMyLKO7SQ3qx6uUCz3gVs0z0B0IdyKMH1Nwa2v%2FE3azNZuepXzLS5YU5e%2FNJ26caJ3uv03uXRob7VLgfTraNS%2FQ0tiqbvr%2FhvRDdiNx%2BIZiUfxvYn3Ev%2B8B9y583h54o6%2B6%2FGpDYkX6VnRpfmcot2xq3l0Nrl6jrRyca6bO8H9kLpYX821QQaXerkXrFaM%2BucNudn%2FNm%2FpmSpJzVL6Hn0VQvx20IthPLwL5QF1DNqCfUhv5x36KBMVo65osIaMFke7aoJ6P1OYN0VqymvN25n3LL9LeF5%2FkD%2Boj%2BedyOvJM6K8wrx6eHA874s8Pk%2F1ZEQrId2qPeSEACt4MunG0S4LAW3%2FYAXFbA57MzJCYRmEz6SELGZ1RmmTGS8GUeoktarJ4w1lZkDe4gzclIEzIO9ATigUpjZXO0JhzQyRKilWh0C%2Fw1A0rFYBVABkh6NhddiIaGH43fAXYcYU9oVbwwwK%2B8NF4WSYDbtz%2F1bR70iljzpT2rLiPOz4sCmdb4lFKn5dvJrLD0r0sjfolkToxoQj1oCd%2BkhOzVNyOrTFHL60mH9d16sws6l7%2Ftai2qevW%2FZ0LqzuzPCk4QsHJc4MqBxStbAgcYYNPfjC1GnTps68rmZ7XyOZ%2BdtBFWM2bU0QUvvEjIG1ax7ru5i602YbYc4caKfqEqxO6wxxoch2shhmS6kRa0zfKByvKTezYDTwep0OjFWCQw6kKTeEk%2FTN5P9Fucm6kN5I%2BWsw6C%2FpOD0%2BC%2FvclTpO49R%2FU3OphdFv5wauUGoak0DVsY2JM9mTyscujYCq4DZ9EHt8oo8MeHHe0Po17QkfG9pxoHrhmt%2Bk7tomgw37OIzVAB7PNnXM1%2FiM%2BJP1Jzv7JvmaIxY355ZIozLdOt3R6NpGtvPbxW36Tukj8mfuc%2Bkj%2FRnuDP%2B1Qdkl%2FpH8G%2F%2Ba%2BIaeWyZu4NeIjFmTQ52TMsnGCrZywdPkbfYSrzGArnBRUo5eynDv3wGlRcp8sNsXuVhMtz8cs0YtMDCUet83lHPZXjd5Y9%2BO%2F8TRxNvfP5T4aSP2b73llkcfveWWrSTrPsxvTLz5w38mXluT3P3b3bvbduzeTce7KXETuw3Gq4CP8rg6aKh1jJVYoky5odwa9dYwYw1jrTXef3kl6uf2%2By7nhX95RVhBl%2Fu0Dp1OMRn7fVpzntFoCimK5qzo%2FqtXO75X%2B3XH6f%2Fm12r7E93zqV97ma9C3xO1U1lHaceWuiu%2FjnoT5kteuqELk8TFrobNE2GSHQ%2FMn3332jkL1sPk1s9N%2FCXRlzif%2BLR2Wt83TFfH3ic7dj29E0RyHUJMmTb23WruNg5LRjyFm88t45hCS4NxobHZwsqSSe%2FTk836pJ5U6ifqib6TrFDzBAEknCG8nIskRSqSmiVW8qy27LSQmZbVln2WExbWoqAQPeKD8RPSitvoGZ%2B5sgtnoH7X%2FpJAn4%2B5x6dMUeAEyHd5cYoVLagu7pxSFy%2FV3pgtHtqo%2FWohxYmUUcqbcRuV6eoba5oar7lqxPDJhWxo2401pf8cVLUn8Z8wxiKQZwXGmE9eVbt5Mx8Uw06zM7jdst22LfxoviTYam3EcsTQZXwz8GXwguF8Fp9nmGaYZ3hUt82yK6tLL1QF1eya0IKsuaF1lnW2tVn3ZEtlodF8rW6cYaKpNjAqS8jKDofK9KUBeldTmi3wMmeWAi5DWJ%2BVlRUUsrPUgbfqV9pusy%2FPW5a%2F3r4m%2F3H7o%2FkHsg4EDa14s%2FM%2B12P5L%2BTHB%2FLOgEMNBKMONcMX9TnwF2D2l4iB%2BpzNOSRHdWVGczwDtZc6QO%2FWD8RFA3HhQDxwQKBIwUoJuP5p3Zx661auTO1M9H0Cd2RlJ2X5RdC32vlVWodobzNTTdyL0hdOpTzGPHbgUNaQQG1gKm50zsWLnOexjJ2E9QSySK7VoCe5npksZmtzdfUe7Km1CuA3wB81Yfsh1uKl12N%2FpFZ3oDOFs7Trw2ya7unwZafSbo%2BWVr0QudGAh2TVZm03PJL1h6wPs%2FhAlt7Ash6UtutRCbXwO5wFlTjtBGrprJyodiOYCbsfwqk7QbYJt%2BKzmEFY0W4IWa2k1QElMVbHIxbPZM%2ByhA7BoULVjhKnCvU6VajUqZaWRZ30fNKp5uRBAPWanD7tKJB1TvOooL9NHlzvSXpIevDaJaH2oW8fx1roe8hLUskUM9K3eimvqgU%2BsdRbgtnJt1VJZ6k05UIAfPj%2BkKFcb9OX02i7nt4TfrtfV47Sr3o1gj5M3fjRX5GEQ%2BFs7caP7n%2BXX%2FjR30LQ48Ii7LHcMufmshybfWzixWtXffblZx%2FmJn4yz2xYXOTPCOFXGhvO%2FfBpHy6MTJ6Wm1Hot9vMdSOnP7bx6AObBo8c5XMEB9gz5o%2BrW%2FvQ%2B3HNnvQlvyYPck%2FCrvAnNc%2BPwIGT80zDjOOMjSbBbUcuxmFHTovVhp0WYsMuRhJkQe%2BiDDchZ5sz7mSaAHU7GSc4qu12TJVmB7LTXyAuVY16nVQoFyLwFWeCnqCubK6LCTkt0%2ByVtp22fTamydZq22I7YTtr45BNsfltRTbW5vasbOs3KOriZaAphmu%2FRLAlu%2Bm14cXUraFyTvNze7VfLkLR0%2FSXJSX9v5zD4NTaNK46%2BfRlnDlYWlKaYya3d%2BvCGeFxrtl3XH17uU666y7sYUM9ial3RzK8n%2BWXTBo9%2BFH8bs8HzyY2AH%2FuBz0zhQ2BjbBDdV5jXmDeyjES7%2BYrSIW5jtSZzxBB83%2FMrM6BZLsNXHjw40N2O6Iq0ujQLIWUs%2F9%2FWAqSeMlEEPFZEYv%2FuxuU2mT%2Bi4UQSx16hUL0%2BtH2600kM2HYsUU37rkau32TK8csycfundNmX79nK2lLuHrmDZ%2B47DTupo4FBn8CsTNgnDrsVe1crqcwKtCAp4FIA3AyTnYA1lwav2dY9HEW84xOFGW9Dvw2YmE8kkfOQgW6N3V6WN1nVUemPyojTmdDbl0OytdF0TDdOiSlb71kbNBrdekkZ5TFSMI8klElfXuuPH2LpVp0SGZ1siQRgnmIS%2BX0%2FFR1ZeRGdQaf9gY6a3A6PYpcKU%2FUXvYpUnUsKdex9L6GYQ%2BTIjDSWlWTvhRhPygRBrv1fwDZclPhirjG98Zgr4q5tcsoLa3ZqKkfcWLogra4IzF6ppT6rQsOWJ30uN4KTsjLiak4%2FNYwJ29U3sGBBHCv768HRzsKCsiAFE%2F1wNMmjafb1U25wlss2S504c%2FxR8JZAycKHtbF5%2FJlaKg4BjfiO%2FAyQQ7hiDAEDxNq8Thhu%2B4Cf0GQctiQkC9H2WFyNTtBfo0Vr5anso3yXPZmeSW%2BU36E3Soclj9iP5cvygaGFcBNc7B%2BNl8uYSvlWlays255mDxBvlHexb7Mvi2fZyUBJqfD4qIzebLD7qS4R7XrzVHMygJLr%2FwAiUgS6Wt4PYfyCqJJ7UXPHtXkyI4yISLZCJE4XqdLPz6rwzSqOuGxLoQ4G0Icz3FgR4iSpEMcOIftfIlEfUSdOG%2BiYaehx8AYGJpNSnQ023I2dWhJ3%2BBg0bxf56hFeynYPf7S68GFl14PplfJkZb%2B28BUrP%2FExVmefiNY9oMTSQeYcia1N4Lpe5ktLUswDUrAl7TSyQwwjB6vTjyIrzn6Bh6X2I43JHad%2FIwECZP4HGcnpL738NjEy6l7ZmNiEjsZ5tWKowcsuRy20sG79Kao6DCYogINeBpwDsgjqVf2h0XBGWQNOiOvEGTlWSthGYbexVubYKPvxPtA2E2GQmMu8tuL7E12hh6GaDthKKqdkVgyBkTt9G2LckZ1uaOrtXvWsCoRLUUwoSkLLkdqxpBo%2Bk0Y2x%2FSejSSuo6nMp%2F6aRjwa8l45dxpsMljhSmJx%2F2%2F0NPuXwWjdlqVlvtYXVwBNTwM1HA7q6DDSZiv5Nn9jIK134Klf9DxtWo0mCutitUNgcVVyVFBgwTF7ZBOv9hs1TguGJlgVjisXUkYcSRxAQcTG6pzqq9ZXT9pgntU6ezr3bCgjOTHi6QrNntElvlzw62NwP5kEs1KxOhvm0iIai0soAq8Fx5kqW68JSueRVCWktUEETbLlT0mgnG%2BmEfHEWspBVrt9y5COdAaNNpheDKejwaqduw3TTRtNjHI5DftNO0z9Zg4E%2B4k1QdoFaYuEkX9tWh9mJaI0fsjqCcr3QcH9CH%2FEFYCMwMkkL6kpFg1KLZoQA7TevJSXQGG0r5oPhl3AOoo0OoYjvOgjiGql2zx4KQHv%2Bv5wUNMnmYP6fZ84SGQ4wFRHgU9IjjSRUrTPQI5blkCCg4dYXeTVu03y9WqFfst9ij2g2lWz2DtfWaQl0ZVgviXSKHyQma9jG9BbvarO%2Fp%2FUhujP6YFSx20XiRCJ%2BnIRjw80cvuhvn5KPW76H%2BwB8ku7gByoZmqZbE4z3a7uNzGNorTbASjiJkaBsb%2Bdy9LeaMh4qBZTh4ZRpuMPiMxejxoNHa7PS8ERtwEy3bCpRssaPkc3cj7zmkeU8waTNtBmoiU8NqJKHUOlrbi%2BfXBip0F%2Fgh%2FN54zISvg7Mz3Z7MHnYZFLaarcgvXLRX42nBqrYYSMXJV8hxyonrVELaV2a6yMSFr1Epf9HtYDTr%2B6oeFtFn%2Bq0kWZlk8bs0r0pdqB1cu958Dz1RrfYyNPx1L%2F8K7F3p4WrP5rKVpo0376eoQ7Ye%2FAt2FF0xvjARGVqnVngkz7rx7zKyDdyVuXq7zu0JZxbY53lsmNpTVQd9Y9AxC4qPab5ccIEUhbFFH6YiX%2FMv0k4MVvXygLDDWOzqw29hlfEP%2FR%2BM7ltftn%2Br0nwTOBAjhhYAwWFCFRwRWKMZgmWEiEJ7BxN9Jnms3GMCFfU41mGw%2BG7Gpbl%2FUZgtSK8UbCvGSyPOkGHEKRxCHOU6Sg8FWJwYf%2FnlV76e%2FC8KyvDkTZ0JGh5KdraRfNVXoq6ZSbmEuxlgo1l6ilvRRqYtsQmHlfKSvuDAGqLhQ6W0pKWyJ0ZD%2BfKglBk9gWy0pBByJ9ZYUgsWWfm3ysleACH1VOhwVVKn%2FlxvaXY61%2F0XiAI6mOKzZOZefDTABe%2BAZnEWqsCvxft9bie%2FYT7948aWeGbvuPfoJVkbCPAwfXl3zPfljXyl3%2BOJQ5q2fa5mXLk5m3EdO9bY371v294dnzly%2BfPbs2%2FqeP5WSGdi%2Bhk5%2BH90zdaap4p%2BiW9T%2BS9XTf6vQ%2Fvvg%2B4%2Bs9v7888U%2BBYnZUFYCwChNJ4xMTEDVCvr5559vV1A6%2F9fPSB6ySDlM%2Fa3IQvagGwGqSXmyD9JPA5QAjOfehObfRFsBZgE8wk1Hj7J%2FQ9v4cjSb5gP9fYAfg7wn%2BT3oQYhvh2eNtJxGNx2Ng2cDIf4wNz2ZFO5HArTzEEAI6h8Lz9YBngZ4KuAqyHdp8b9BmXL0MORtoFjIRKsg70GAyQCbmEyNrgjK%2ByB9P8R1AHoAo9ZX2s9UvZOhzBGyJ%2FkP2qb4HZVz7XNTCkgFwFsIMVMBXgRuFMGITwL3AIuQlsF%2B0NUjpIdy%2BosIGbYjZOxByHQATATIs8CEWOYiZOtEyAHg7EbItQUh9zmEMqDOjO8Ryvw3hHxQLnA1ADzPykUoeyhCOVGEQs0IhZsQyoO%2B5AMMHINQAQWgK3QBrEFoMJQtzkaoBPoShamMQt1Doe3y6xAaVoPQcOjTCKhn5FmEKgcCLAWAvqjQpgp1VmUAdNL%2FoalJwUj0I6pAmxEPu7KCCtE0GG0FeRVxiOyf2lplADbsA4CHEPoB2gAYpDIvdgiGYrUTsMWm4XZHpLgr2Q2RYSVafsEjxa1Hmb1oJiqB7L3t02j23g61pljDJcNTuHCwhtvF1GPBVuyr8gBZIQBBpnRsIsBmgJ0AxwF46NBe9AVAEoBhdjNPt9f6oIbnoCJTlY15DoanQvguQBKAgd4%2FB2N5Dv2QzmGhV8%2BApqDNP6NReZlngMoEoQLQCrAP4F0ADi2GcCdAEoCB2NPw7GlEmKeZp9oVn1IlM79FqwEI8zgyYeq%2BdjPbOxSNN491mKzFapXCPIrqAQiKM%2BNRNwCBah8EsgeplmHq2gsGayys65CNxQqU3wSd3gQd2QRNtkGItbQKQMtv6rA6aPX3tJvMGt1v2ouiqUiH4iquBy6sRJiZx9yCgsjHrAI8APAcwJmAZzNzwdSg%2FVQ7TEpxK7RXCcUrGTts3z6minGgYsA1jIf%2BTx0otqzdmGpnWXtufjGMuJpxaUVMjAFFAYuM0F7s8x9hVI3560E50%2F6tb1fsxceYexkB2aBUK5Ry%2BkzHGBlmVtZGMrVDMhRvqdLDwmsDIFDqFqhipxaqzC3tUFGVmRnNZMCG5GNuhCVvB1zLDNDwLuYpVAv4yY5Qhq%2F7CPOwRvUQrRSaH5kSrZEdBmNxd5XEjISnceYBmIAHtMa3dISGFqOqEJOLigAI8Hg1xFZrQr8RYhth1jbCTG2EmdoIndoI0oeYDfBkA5QpZG5HzcwKtAVgJ8SpWNnbgaFdWiQ7t7iLcTMuYIxyBFiJIdfTIRlpz1ztFqtWzNWhNxZXHmNuBTm%2FFepUmaUdTlfx4iNMvjaUgR0uLyVobgdxPcY4U1MDhA46JceYDGAEZUwmM6Dd7otX%2BSBNBRmsBfIOOUGZRD4gH9Hppv%2FtU8N%2FTOM%2FpfG%2Fp3Cym5xILQryPsU9VRnkS3pfTU6hnRAj5Ah5DYx4H%2FmMdNJekE9JF6oEfBLScwF3AS4BfLg98Javk3R2AIK%2BP9FucNDBktfaI4XpiC8nHXF60xGLo7gqh7xKXkEZUMUngLMBv0K6wQ7xkeOAXYC7yVL0FuCDYGoOB3wgjf9AjlIRJy%2BTQ2go4I52I%2B1CvF2gaF87T9FL7SiVqi%2F0HSUvkb3IA0V%2F1x7yQO7ujlC2z3QE6sPkObK0PdNnqZLJU7gBn4NCbegkxbBHPt1eRivZ0n7U7%2BsiW8gW1VWm5qgF6vNMUU5RQdHzjD%2FHX%2BAv8z%2Fvr1LIA6BAdoI5gskmCMuQn4D0AKgAW8iGdrYsXtUHY6LjIqgVwjYt1gRhsxZDECqXnp7VYpXkXjQRgEAdqwBWA7QC3AUW3BZyO8BvAO4AuFPLWQqwDGAFaJNmoGgGimagaNYomoGiGSiagaJZo2jWWl8GQCmagKIJKJqAokmjaAKKJqBoAoomjYL2twkomjSKeqCoB4p6oKjXKOqBoh4o6oGiXqOoB4p6oKjXKFSgUIFCBQpVo1CBQgUKFShUjUIFChUoVI2iCCiKgKIIKIo0iiKgKAKKIqAo0iiKgKIIKIo0Cj9Q%2BIHCDxR%2BjcIPFH6g8AOFX6PwA4UfKPwahQIUClAoQKFoFApQKEChAIWiUSja%2FCwDoBQ9QNEDFD1A0aNR9ABFD1D0AEWPRtEDFD1A0UNW7GdOVL0OJCeA5ASQnNBITgDJCSA5ASQnNJITQHICSE6kh75UYwYBsVkFsBqgFYDSdgNtN9B2A223RtutidcyAEobB4o4UMSBIq5RxIEiDhRxoIhrFHGgiANFXKNoA4o2oGgDijaNog0o2oCiDSjaNIo2TXCXAVCK%2F3%2Bh%2FP%2BeGnIXbhBhryWt4MBSvBp9r%2BFV6KSG70T7NXwHel7Dv0F3a%2Fh2VKbhFWAGUgz1aXgp2Ga43VdmqnKACpgIMBNgMcBOgH0AxwEELfYuwBcASVKqZrEmYaKwU9gnHBe4fUKPQEz8RH4nv48%2FznP7%2BB6e%2BKu8xKDpUeoQb9bC1RD%2BAACbCISVWqwS%2FPmJAISUwjdKoqq51%2F9DPn43Hx%2FPx%2Fvy8eZ8XCWRqzCraTo%2FKgP31ocbVH1opO8kQFkoPBI00wOHvnf62kNDfJ34aArlqRHA3wPsB3ge4G6AMoBigAKAHACflpcP5RvUrHSVRwHCAAEAP20COeiZgsUsql3EgJ%2FveN2A6P8Kag%2FnAt2R9nARoM728ERAL7eHZ%2FuqJHwIhalVhA%2FCzO0FvK%2Fddxoe%2Fy6FXmz3HQG0u90XBRRrDw8CdG17%2BE%2B%2BKgOeBnY9JZ2axlNg3BRPbvdNh2KT2n15gCLt4RAtnQ8N5cDTPNyATgPOSVNlp1oKtvuGA8pq95XT0iIK04nHPCrQuscBUMx0QId%2B6MINLFZ1vl7fw77vgfw7YCyIx6f%2BThbQuzn0H4PJvqMFv4XCVb72KpmWh%2F1hfxrHKT7oez5ng%2B8JqAvnHPI95hvke6CgU4Ts%2B6HfG7Qm2n13g3u8V7X6Wn1FvqUFp323%2Bsb5Zvkm%2B2I5kN%2Fuu853lHYTNeIGsveQrx4qHAujyGn3XZXTqXWx1nebT%2FWFfeX%2Bo5S%2FaGiq3rKCo5QDqDjV%2BkDgb35OJ5XxaWWd2KzmC2eFLcK1wihhuBAUsoQBQqZgEy2iIhpFvSiLosiLrEhEJNroWWKE%2Bo42XnMheZaGrBZXCA1JytkkWCRoHIpbmTpSN2UUrot3z0F1s%2F3x81OCnVieNCPOBUfhuKUO1U0dFR8aqesUkpPjZZG6uFB%2FbcN%2BjB9ohNw4Wd%2BJ0dSGTpykWfd66f%2Bg3Y%2FRvfd7uxDG7nvvb2xELsfySlelZaS5vLbmfwia0uFlv3VzXR7NjG%2Btm9IQ35P5%2F0o5g960YSgAOwGNlg7aStWEylCSRWHdogyBxDgUjSRKTlymdYdkJ2iF1J6GlGS9se047cJPyC6oRwekaVSaNIk%2FwQ%2FY%2F%2BjecxLoptxmcJ7z3uf3kG0IDsYubWHhrub26WfcoXbJ7%2FMl21ryZRSus8yP%2BX37DerzY8sF7DfDYDSXASNPUQC2YxIRMfg8MRGDPoq5OlQHTkIBXLFE6oyrF0uMy3PIRWvRtiJRZIxCyJoxa4XcY2DEQF0rqtcZJYucgxTnyCJ7Yc%2BYI0EARBMYwsH3OuZI4Fgw2tgiSoK0N0ibxcpxW0aImaOTlDk6AUb9zzQyVW7RDCYr3PR3INsjyAP69cNlhX46F8VoEiS7AdcH5xeXKIcjGsgji05kS4yaqwzzCs1N2YrIyn7rRCt9ZM2betOWh5a76HUd469YXzaxnG6Gsy46czBWz8gwG2juYSwDYxkYq6f3WCz7Csf9ayfaISbehGZywe8VYQwPqpJrPjoYv8IBvTyVKpPqbZ5wN2RPdelD2aQlyGjSDM1AE7zP0FTGnZ0TU2VyKlVvuZvEdADqQ9kkadMShHAZQ59KZ%2B8cHCpUH2b3mYeJmSvEvrLgCec%2By%2FC4TxIvM%2FlZKQgCDw%2BB6hHSp8%2FP%2BvQlLqooFCDUwHJB9yLV5XJMF%2B3u2j%2FufoFRhRfB%2BRgOSyqHC7P1Isy6Cnz4ICzwOFXwF8e11vufcAX%2FCBnmcfz1vMGmz%2Fz14omC8xd%2F0WjHEqarKOfHUgvvDnagKkollvqhBoWpMtWmnVAJtbCDf3r7PgOlMMNL6bwxyxFf9dKGgKLvkni9OMT7Nn9cY4FDLKiqq3rsBwXyb1OnO6hAo28a1ku8esy9n3ZIrPcSJ9ATcfQgrRYklZgxYJViJ%2FHZ5rBNcEbIH0uJQPQNCmVuZHN0cmVhbQplbmRvYmoKMjUgMCBvYmoKPDwKL0xlbmd0aCAxMwovRmlsdGVyIC9GbGF0ZURlY29kZQo%2BPgpzdHJlYW0NCnic%2B%2F%2BfyuADAD67UaANCmVuZHN0cmVhbQplbmRvYmoKeHJlZgowIDI2CjAwMDAwMDAwMDAgNjU1MzUgZg0KMDAwMDAwMDAxNSAwMDAwMCBuDQowMDAwMDAwMDc4IDAwMDAwIG4NCjAwMDAwMDAxMzUgMDAwMDAgbg0KMDAwMDAwMDI0NyAwMDAwMCBuDQowMDAwMDAwMzQyIDAwMDAwIG4NCjAwMDAwMDAzOTEgMDAwMDAgbg0KMDAwMDAwNjU5MyAwMDAwMCBuDQowMDAwMDA2NjI1IDAwMDAwIG4NCjAwMDAwMDY2NjggMDAwMDAgbg0KMDAwMDAwNjcyMiAwMDAwMCBuDQowMDAwMDA2ODYzIDAwMDAwIG4NCjAwMDAwMDcwMTAgMDAwMDAgbg0KMDAwMDAwNzM5NyAwMDAwMCBuDQowMDAwMDA3ODA4IDAwMDAwIG4NCjAwMDAwMDgzNDcgMDAwMDAgbg0KMDAwMDAwODc5NSAwMDAwMCBuDQowMDAwMDA4ODcwIDAwMDAwIG4NCjAwMDAwMDkxNTEgMDAwMDAgbg0KMDAwMDAwOTMyNiAwMDAwMCBuDQowMDAwMDA5NDAxIDAwMDAwIG4NCjAwMDAwMDk2OTYgMDAwMDAgbg0KMDAwMDAwOTkxNSAwMDAwMCBuDQowMDAwMDEyMzA0IDAwMDAwIG4NCjAwMDAwMTIzOTEgMDAwMDAgbg0KMDAwMDAzMTA3MSAwMDAwMCBuDQp0cmFpbGVyCjw8Ci9Sb290IDEgMCBSCi9JRCBbPDAxNTZCQkE4NjI5NjdGNEMxRTlDNzREOUZFQzY5M0UxPiA8MDE1NkJCQTg2Mjk2N0Y0QzFFOUM3NEQ5RkVDNjkzRTE%2BXQovU2l6ZSAyNgo%2BPgpzdGFydHhyZWYKMzExNTgKJSVFT0YK%22%2C%22ULDNoBatchNo%22%3A%22SNU06032024220240011752%22%2C%22cn38List%22%3A%5B%224795292024%22%5D%2C%22ULDNo%22%3A%22SNU060322024%22%7D&data_digest=Ot44TqQLNp8U4Ydml2V%2BpA%3D%3D&partner_code=de316159604032ef042935f43ffc3e2b&from_code=CGOP&msg_type=cnge.cn38.callback&msg_id=SNU06032024220240011752";
    dump("Post body is: \n" . json_encode($post_data)) . "\n";
    curl_setopt($ch, CURLOPT_POSTFIELDS,  ($post_data));
    curl_setopt($ch, CURLOPT_POST, 1);
    dump("Start to run...\n");
    $output = curl_exec($ch);
    curl_close($ch);
    dd("Finished, result data is: \n" .   ($output));
}



Route::get('/cnge-airline-arrive', function (Request $request) {
    return cngeAirlineArrive();
});
function cngeAirlineArrive()
{

    $content = [
        "needUnbind" => "false",
        "handoverParam" => [
            "country" => "China",
            "zipCode" => "310012",
            "city" => "Hangzhou City",
            "telephone" => "098-234234",
            "portCode" => "GRU",
            "addressId" => "12356",
            "mobilePhone" => "15678123422",
            "street" => "Chiang Village Street",
            "district" => "West Lake District",
            "name" => "Zhang San",
            "detailAddress" => "680 Wenyi West Road",
            "state" => "Zhejiang Province",
            "email" => "18934@qq.com"
        ],
        "outSortCode" => "FR001",
        "orderCodeList" => [
            "LP00667794278588"
        ],
        "weight" => "150",
        "locale" => "zh_cn",
        "bigBagTrackingNumber" => "BP0000001",
        "syncConfirm" => "true",
        "weightUnit" => "g"
    ];
    $linkUrl = 'https://link.cainiao.com/gateway/custom/open_integration_test_env';
    $appSecret = '2A1X6281822ts3068j1F8Wdq99C76119';   // APPKEY对应的秘钥 
    $cpCode = 'de316159604032ef042935f43ffc3e2b';     //  调用方的CPCODE 
    $msgType = 'cnge.airline.arrive';  // 调用的API名 
    $toCode = 'CGOP';        //  调用的目标TOCODE，有些接口TOCODE可以不用填写
    $digest = base64_encode(md5(json_encode($content) . $appSecret, true)); //生成签名  
    echo ('digest is ' . $digest);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $linkUrl);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/x-www-form-urlencoded']);
    $post_data = 'msg_type=' . $msgType
        . '&to_code=' . $toCode
        . '&logistics_interface=' . urlencode(json_encode($content))
        . '&data_digest=' . urlencode($digest)
        . '&logistic_provider_id=' . urlencode($cpCode);

    dump("Post body is: \n" . json_encode($post_data)) . "\n";
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_POST, 1);
    dump("Start to run...\n");
    $output = curl_exec($ch);
    curl_close($ch);
    dd("Finished, result data is: \n" .   ($output));
}

// done cnge.order.create
// done cnge.waybill.get
// done cnge.order.update
// done cnge.bigbag.create
// cnge.cn38.request
// cnge.cn38.callback
// cnge.airline.receive
// cnge.airline.arrive
// cnge.track.get
