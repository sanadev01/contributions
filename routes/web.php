<?php

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProfitPackage;
use Illuminate\Support\Facades\Artisan;
use App\Services\StoreIntegrations\Shopify;
use App\Http\Controllers\Admin\HomeController;
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
            Route::get('consolidate-domestic-label', ConsolidateDomesticLabelController::class)->name('order.consolidate-domestic-label');
            Route::get('order/{order}/us-label', [OrderUSLabelController::class, 'index'])->name('order.us-label.index');
            Route::resource('orders.usps-label', OrderUSPSLabelController::class)->only('index','store');
            Route::resource('orders.ups-label', OrderUPSLabelController::class)->only('index','store');
            Route::get('order-ups-label-cancel-pickup/{id?}', [\App\Http\Controllers\Admin\Order\OrderUPSLabelController::class, 'cancelUPSPickup'])->name('order.ups-label.cancel.pickup');
        });

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

Route::get('order/{order}/label/get', function (App\Models\Order $order) {

    /**
     * Sinerlog modification
     */
    if ( $order->sinerlog_url_label != '' ) {
        return redirect($order->sinerlog_url_label);
    } else {
        if ( !file_exists(storage_path("app/labels/{$order->corrios_tracking_code}.pdf")) ){
            return apiResponse(false,"Lable Expired or not generated yet please update lable");
        }
    }    

    return response()->download(storage_path("app/labels/{$order->corrios_tracking_code}.pdf"),"{$order->corrios_tracking_code} - {$order->warehouse_number}.pdf",[],'inline');
})->name('order.label.download');

Route::get('order/{order}/us-label/get', function (App\Models\Order $order) {
    if ( !file_exists(storage_path("app/labels/{$order->us_api_tracking_code}.pdf")) ){
        return apiResponse(false,"Lable Expired or not generated yet please update lable");
    }
    return response()->download(storage_path("app/labels/{$order->us_api_tracking_code}.pdf"),"{$order->us_api_tracking_code} - {$order->warehouse_number}.pdf",[],'inline');
})->name('order.us-label.download');

Route::get('test-label',function(){
    
    $orders = [
        'NA281723761BR',
        'NA287299365BR',
        'NA281723829BR',
        'NA287297982BR',
        'NA281723792BR',
        'NA281725135BR',
        'NA281725100BR',
        'NA281723758BR',
        'NA287299351BR',
        'NA287297996BR',
        'NA281723789BR',
        'NA287299348BR',
        'NA281725073BR',
        'NA281723801BR',
        'NA287298033BR',
        'NA281725095BR',
        'NA287298002BR',
        'NA281725127BR',
        'NA281725113BR',
        'NA281725087BR',
        'NA281725158BR',
        'NA281725144BR',
        'NA287299325BR',
        'NA287299334BR',
        'NA281725175BR',
        'NA281723775BR',
        'NA287298016BR',
        'NA287298020BR',
        'NA281725484BR',
        'NA287299714BR',
        'NA287299731BR',
        'NA281725436BR',
        'NA287299728BR',
        'NA287299705BR',
        'NA287299759BR',
        'NA281725498BR',
        'NA281725467BR',
        'NA287299745BR',
        'NA281725453BR',
        'NA281725475BR',
        'NA299752635BR',
        'NA299756867BR',
        'NA299752689BR',
        'NA299752661BR',
        'NA299752692BR',
        'NA207472416BR',
        'NA167288635BR',
        'NA210209712BR',
        'NA167288621BR',
        'NA210209672BR',
        'NA207472433BR',
        'NA207472402BR',
        'NA210209690BR',
        'NA207472393BR',
        'NA210209669BR',
        'NA210209709BR',
        'NA167288652BR',
        'NA167288666BR',
        'NA167288649BR',
        'NA210209726BR',
        'NA287298957BR',
        'NA287298930BR',
        'NA287298943BR',
        'NA281724719BR',
        'NA210214261BR',
        'NA281724909BR',
        'NA302420834BR',
        'NA302416066BR',
        'NA302420560BR',
        'NA302415613BR',
        'NA302415984BR',
        'NA281724855BR',
        'NA281724869BR',
        'NA281723832BR',
        'NA207471322BR',
        'NA302522011BR',
        'NA207475355BR',
        'NA207476885BR',
        'NA207474213BR',
        'NA210214876BR',
        'NA210214862BR',
        'NA210214859BR',
        'NA287297673BR',
        'NA287297660BR',
        'NA287297687BR',
        'NA287297656BR',
        'NA210215369BR',
        'NA281722165BR',
        'NA281722559BR',
        'NA210213955BR',
        'NA281723452BR',
        'NA210215050BR',
        'NA210215046BR',
        'NA281723449BR',
        'NA281722409BR',
        'NA210215324BR',
        'NA210215029BR',
        'NA281722219BR',
        'NA210214947BR',
        'NA281722298BR',
        'NA207476126BR',
        'NA287298577BR',
        'NA287298563BR',
        'NA210213685BR',
        'NA210214641BR',
        'NA207476925BR',
        'NA210214540BR',
        'NA281724033BR',
        'NA281723982BR',
        'NA281723965BR',
        'NA281724047BR',
        'NA281724078BR',
        'NA281724016BR',
        'NA281723996BR',
        'NA287298339BR',
        'NA281724020BR',
        'NA281724055BR',
        'NA287298245BR',
        'NA281723979BR',
        'NA281724002BR',
        'NA281724064BR',
        'NA287298237BR',
        'NA281723850BR',
        'NA287298095BR',
        'NA287298081BR',
        'NA281723951BR',
        'NA287298078BR',
        'NA207475752BR',
        'NA210213411BR',
        'NA210213408BR',
        'NA207475766BR',
        'NA207475770BR',
        'NA207475783BR',
        'NA207474099BR',
        'NA210211526BR',
        'NA207474125BR',
        'NA210211530BR',
        'NA207474111BR',
        'NA207474108BR',
        'NA207476599BR',
        'NA207476585BR',
        'NA210214187BR',
        'NA207476571BR',
        'NA207476608BR',
        'NA210213345BR',
        'NA207475721BR',
        'NA210213399BR',
        'NA210213368BR',
        'NA210213371BR',
        'NA210213354BR',
        'NA207475735BR',
        'NA210213385BR',
        'NA207475749BR',
        'NA210213337BR',
        'NA281724648BR',
        'NA281724617BR',
        'NA287298665BR',
        'NA287298651BR',
        'NA281724492BR',
        'NA281724550BR',
        'NA287298838BR',
        'NA287298740BR',
        'NA287298775BR',
        'NA281724651BR',
        'NA281724577BR',
        'NA281724546BR',
        'NA281724679BR',
        'NA287298815BR',
        'NA287298872BR',
        'NA287298784BR',
        'NA287298679BR',
        'NA281724515BR',
        'NA287298886BR',
        'NA281724461BR',
        'NA287298682BR',
        'NA287298824BR',
        'NA287298890BR',
        'NA281724475BR',
        'NA287298869BR',
        'NA287298909BR',
        'NA287298722BR',
        'NA281724625BR',
        'NA281724489BR',
        'NA281724585BR',
        'NA281724603BR',
        'NA287298807BR',
        'NA287298855BR',
        'NA281724563BR',
        'NA281724532BR',
        'NA287298798BR',
        'NA287298841BR',
        'NA281724665BR',
        'NA287298696BR',
        'NA281724634BR',
        'NA287298767BR',
        'NA287298705BR',
        'NA287298736BR',
        'NA287298719BR',
        'NA287298753BR',
        'NA281724529BR',
        'NA281724458BR',
        'NA281724501BR',
        'NA281724594BR'
    ];

    $anjunOrders = Order::whereIn('corrios_tracking_code', $orders)->get();
    $corriosBrazilRepository = new App\Repositories\CorrieosBrazilLabelRepository();
    foreach ($anjunOrders as $order) {
       $order->update([
        'shipping_service_id' => 1,
        'shipping_service_name' => 'Packet Standard'
       ]);
    }



    // dd(132);
    // $labelPrinter = new CN23LabelMaker();

    // $order = Order::find(53654);
    // $labelPrinter->setOrder($order);
    // $labelPrinter->setService(2);

    // return $labelPrinter->download();
});

Route::get('find-container/{order}', [HomeController::class, 'findContainer'])->name('find.container');

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth');
