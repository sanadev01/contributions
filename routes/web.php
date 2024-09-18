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
