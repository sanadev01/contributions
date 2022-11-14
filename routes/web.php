<?php

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;
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

Route::get('test-label/{id?}/d/{dno?}',function($id, $dNo){

    $delivery = DeliveryBill::find($id)->update([
        'cnd38_code' =>$dNo
    ]);
    dd($delivery);
    $order = DB::table('orders')->where('id',$id)->update([
        'deleted_at' => null
    ]);
    
    dd($order);
    $labelPrinter = new CN23LabelMaker();

    $order = Order::find(90354);
    $labelPrinter->setOrder($order);
    $labelPrinter->setService(2);
    
    return $labelPrinter->download();
});

Route::get('test-route/{data}',function($data){
    
    $data = array (
      array("NA638260810BR",3357),
      array("NA638260806BR",4050),
      array("NA637513628BR",6829),
      array("NA638260797BR",96),
      array("NA637513614BR",980),
      array("NA637513605BR",760),
      array("NA638260783BR",90),
      array("NA637513591BR",229),
      array("NA637513588BR",476),
      array("NA637513565BR",624),
      array("NA637513574BR",2000),
      array("NA638260770BR",0),
      array("NA637513557BR",2227),
      array("NA637513543BR",96),
      array("NA638260752BR",0),
      array("NA638260766BR",0),
      array("NA637513530BR",0),
      array("NA638260749BR",0),
      array("NA637513526BR",0),
      array("NA637513512BR",0),
      array("NA637513509BR",0),
      array("NA638260718BR",0),
      array("NA637513490BR",0),
      array("NA638260704BR",0),
      array("NA637513486BR",0),
      array("NA638260620BR",0),
      array("NA638260616BR",0),
      array("NA637513424BR",0),
      array("NA638260602BR",0),
      array("NA637513472BR",0),
      array("NA638260681BR",0),
      array("NA638260695BR",0),
      array("NA637513469BR",0),
      array("NA638260678BR",3203),
      array("NA638260664BR",0),
      array("NA637513455BR",0),
      array("NA637513441BR",0),
      array("NA638260655BR",0),
      array("NA638260647BR",0),
      array("NA638260633BR",0),
      array("NA637513438BR",0),
      array("NA633348815BR",0),
      array("NA633467905BR",0),
      array("NA633348801BR",0),
      array("NA633348792BR",0),
      array("NA633348789BR",0),
      array("NA633348775BR",0),
      array("NA633467891BR",0),
      array("NA633467891BR2",0),
      array("NA633348761BR",0),
      array("NA633467888BR",0),
      array("NA633467865BR",0),
      array("NA633348758BR",0),
      array("NA633467874BR",0),
      array("NA633467857BR",0),
      array("NA633467843BR",0),
      array("NA633348744BR",0),
      array("NA633467830BR",0),
      array("NA633467826BR",0),
      array("NA633467812BR",0),
      array("NA633467809BR",0),
      array("NA633467772BR",0),
      array("NA633348727BR",0),
      array("NA633467790BR",0),
      array("NA633467769BR",0),
      array("NA633348713BR",0),
      array("NA633467755BR",0),
      array("NA633467786BR",0),
      array("NA633467741BR",0),
      array("NA633348700BR",0),
      array("NA633348695BR",0),
      array("NA633467738BR",0),
      array("NA633467724BR",0),
      array("NA633467707BR",0),
      array("NA633467715BR",0),
      array("NA633348687BR",0),
      array("NA633348673BR",0),
      array("NA633467698BR",0),
      array("NA633467684BR",0),
      array("NA633348660BR",0),
      array("NA633348656BR",0),
      array("NA633348642BR",0),
      array("NA633467675BR",0),
      array("NA633348639BR",0),
      array("NA633467667BR",0),
      array("NA633348625BR",0),
      array("NA633467653BR",0),
      array("NA633348611BR",0),
      array("NA633348608BR",0),
      array("NA633467640BR",0),
      array("NA633467636BR",0),
      array("NA633467622BR",0),
      array("NA633348599BR",0),
      array("NA633348585BR",0),
      array("NA633467619BR",0),
      array("NA633348571BR",0),
      array("NA633467605BR",0),
      array("NA633348568BR",0),
      array("NA633467596BR",0),
      array("NA633348554BR",0),
      array("NA633348537BR",0),
      array("NA633348545BR",0),
      array("NA633348510BR",0),
      array("NA633467579BR",0),
      array("NA633348506BR",0),
      array("NA633467582BR",0),
      array("NA633467565BR",0),
      array("NA633348523BR",0),
      array("NA633348497BR",0),
      array("NA633467548BR",0),
      array("NA633348483BR",0),
      array("NA633467551BR",0),
      array("NA633467525BR",0),
      array("NA633348470BR",0),
      array("NA633467534BR",0),
      array("NA633348466BR",0),
      array("NA633348452BR",0),
      array("NA633348435BR",0),
      array("NA633348449BR",0),
      array("NA633467517BR",0),
      array("NA633467477BR",0),
      array("NA633467494BR",0),
      array("NA633467485BR",0),
      array("NA633467450BR",0),
      array("NA633467463BR",0),
      array("NA633348421BR",0),
      array("NA633348418BR",0),
      array("NA633467503BR",0),
      array("NA633348404BR",0),
      array("NA623510272BR",0),
      array("NA623510286BR",0),
      array("NA624012557BR",0),
      array("NA623510140BR",0),
      array("NA624012543BR",0),
      array("NA623510241BR",0),
      array("NA623510255BR",0),
      array("NA623510269BR",0),
      array("NA624012530BR",0),
      array("NA624012526BR",0),
      array("NA623510238BR",0),
      array("NA623510224BR",0),
      array("NA624012512BR",0),
      array("NA624012512BR2",0),
      array("NA624012490BR",0),
      array("NA624012509BR",0),
      array("NA623510215BR",0),
      array("NA624012486BR",0),
      array("NA623510198BR",0),
      array("NA623510207BR",0),
      array("NA623510184BR",0),
      array("NA623510167BR",0),
      array("NA624012472BR",0),
      array("NA624012469BR",0),
      array("NA623510175BR",0),
      array("NA623510136BR",0),
      array("NA623510153BR",0),
      array("NA620752663BR",0),
      array("NA620066444BR",0),
      array("NA620752650BR",0),
      array("NA620752646BR",0),
      array("NA620066435BR",0),
      array("NA620066427BR",0),
      array("NA620066413BR",0),
      array("NA620066400BR",0),
      array("NA620066395BR",0),
      array("NA620066387BR",0),
      array("NA620752629BR",0),
      array("NA620752632BR",0),
      array("NA620066360BR",0),
      array("NA620752601BR",0),
      array("NA620752615BR",0),
      array("NA620066342BR",0),
      array("NA620752592BR",0),
      array("NA620066356BR",0),
      array("NA620752589BR",0),
      array("NA620752544BR",0),
      array("NA620066339BR",0),
      array("NA620752561BR",0),
      array("NA620752558BR",0),
      array("NA620752527BR",0),
      array("NA620066325BR",0),
      array("NA620752513BR",0),
      array("NA620752535BR",0),
      array("NA620066299BR",0),
      array("NA620066308BR",0),
      array("NA620066311BR",0),
      array("NA620752500BR",0),
      array("NA616006492BR",0),
      array("NA616144715BR",0),
      array("NA616144701BR",0),
      array("NA616144692BR",0),
      array("NA616144689BR",0),
      array("NA616006489BR",0),
      array("NA616144675BR",0),
      array("NA616006475BR",0),
      array("NA616006461BR",0),
      array("NA616006458BR",0),
      array("NA616006435BR",0),
      array("NA616006444BR",0),
      array("NA616144661BR",0),
      array("NA616144661BRa",0),
      array("NA616144658BR",0),
      array("NA616006427BR",0),
      array("NA616144635BR",0),
      array("NA616144644BR",0),
      array("NA616144644BRb",0),
      array("NA616006413BR",0),
      array("NA616006387BR",0),
      array("NA616144627BR",0),
      array("NA616144600BR",0),
      array("NA616144613BR",0),
      array("NA616006400BR",0),
      array("NA616006395BR",0),
      array("NA616006373BR",0),
      array("NA616006360BR",0),
      array("NA616006356BR",0),
      array("NA616144595BR",0),
      array("NA616006342BR",0),
      array("NA616144587BR",0),
      array("NA616144573BR",0),
      array("NA616144560BR",0),
      array("NA616006339BR",0),
      array("NA616006325BR",0),
      array("NA616006271BR",0),
      array("NA616006311BR",0),
      array("NA616006285BR",0),
      array("NA616006308BR",0),
      array("NA616006299BR",0),
      array("NA645421680BR",0),
      array("NA645642540BR",0),
      array("NA645642536BR",0),
      array("NA645421676BR",0),
      array("NA645421659BR",0),
      array("NA645642522BR",0),
      array("NA645421662BR",0),
      array("NA645642519BR",0),
      array("NA645642505BR",0),
      array("NA645421645BR",0),
      array("NA645642496BR",0),
      array("NA645421631BR",0),
      array("NA645421628BR",0),
      array("NA645642482BR",0),
      array("NA645421605BR",0),
      array("NA645421614BR",0),
      array("NA645642479BR",0),
      array("NA645421591BR",0),
      array("NA645421588BR",0),
      array("NA645642465BR",0),
      array("NA645421574BR",0),
      array("NA645642451BR",0),
      array("NA645421565BR",0),
      array("NA645642448BR",0),
      array("NA645642434BR",0),
      array("NA645421557BR",0),
      array("NA645642425BR",0),
      array("NA645421486BR",0),
      array("NA645642315BR",0),
      array("NA645642332BR",0),
      array("NA645421543BR",0),
      array("NA645642417BR",0),
      array("NA645642403BR",0),
      array("NA645642394BR",0),
      array("NA645421530BR",0),
      array("NA645421512BR",0),
      array("NA645642385BR",0),
      array("NA645421526BR",0),
      array("NA645421490BR",0),
      array("NA645421509BR",0),
      array("NA645642377BR",0),
      array("NA645642363BR",0),
      array("NA645642346BR",0),
      array("NA645642350BR",0),
      array("NA645642329BR",0),
      array("NA641795718BR",0),
      array("NA641666404BR",0),
      array("NA641795704BR",0),
      array("NA641795695BR",0),
      array("NA641795681BR",0),
      array("NA641666395BR",0),
      array("NA641666381BR",0),
      array("NA641666378BR",0),
      array("NA641795678BR",0),
      array("NA641666364BR",0),
      array("NA641666347BR",0),
      array("NA641666316BR",0),
      array("NA641666333BR",0),
      array("NA641795664BR",0),
      array("NA641666276BR",0),
      array("NA641795655BR",0),
      array("NA641666293BR",0),
      array("NA641666320BR",0),
      array("NA641666280BR",0),
      array("NA641666302BR",0),
      array("NA641795620BR",0),
      array("NA641795633BR",0),
    );
    foreach ($data as $info) {
        $order = Order::where('user_id', '1678')->where('tracking_id', $info[0])->first();
        if($order){
            $order->recipient->update([ 'street_no' => $info[1]]);
        }
    }

    return "House Nos. Successfully updated";


});

Route::get('find-container/{container}', [HomeController::class, 'findContainer'])->name('find.container');

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth');
