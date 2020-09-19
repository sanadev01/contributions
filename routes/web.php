<?php

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

Route::get('/', function () {
    return redirect('login');
});

Route::resource('calculator', CalculatorController::class)->only(['index', 'store']);
Route::get('/home', function () {
    return redirect()->route('admin.home');
});

Auth::routes();

Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class,'logout'])->name('logout');


Route::namespace('Admin')
    ->middleware(['auth'])
    ->as('admin.')
    ->group(function () {

        Route::get('dashboard', 'HomeController')->name('home');

        Route::resource('parcels', PreAlertController::class);
        Route::resource('import-excel', ImportExcelController::class)->only(['index','store']);
        Route::resource('billing-information', BillingInformationController::class);

        Route::resource('services', HandlingServiceController::class)->except('show');
        Route::resource('addresses', AddressController::class);
        Route::resource('shipping-services', ShippingServiceController::class);

        Route::resource('orders',OrderController::class)->only('index','destroy');

        Route::namespace('Order')->group(function () {
            Route::resource('orders.sender',OrderSenderController::class)->only('index','store');
            Route::resource('orders.recipient',OrderRecipientController::class)->only('index','store');
            Route::resource('orders.services',OrderServicesController::class)->only('index','store');
            Route::resource('orders.order-details', OrderItemsController::class)->only('index','store');
            Route::resource('orders.order-invoice', OrderInvoiceController::class)->only('index','store');
            Route::resource('orders.label', OrderLabelController::class)->only('index','store');
        });

        Route::resource('payment-invoices', PaymentInvoiceController::class)->only(['index','store','destroy']);

        Route::namespace('Payment')
            ->prefix('payment-invoices')
            ->as('payment-invoices.')
            ->group(function(){
                Route::resource('orders', OrdersSelectController::class)->only(['index','store']);
                Route::resource('invoice', OrdersInvoiceController::class)->only(['show','store','edit','update']);
                Route::resource('invoice.checkout', OrdersCheckoutController::class)->only(['index','store']);
        });

        Route::namespace('Rates')
            ->prefix('rates')
            ->as('rates.')
            ->group(function () {
                Route::resource('profit-packages', ProfitPackageController::class); 
                Route::resource('shipping-rates', RateController::class)->only(['create', 'store', 'index']);
            });

        Route::resource('settings', SettingController::class)->only(['index', 'store']);
        Route::resource('profile', ProfileController::class)->only(['index', 'store']);
        Route::resource('users', UserController::class)->only(['index','destroy']);
        Route::post('users/export', UserExportController::class)->name('users.export.index');
        Route::resource('users.setting', UserSettingController::class)->only('index','store');

        Route::resource('roles', RoleController::class);
        Route::resource('roles.permissions', RolePermissionController::class);

        Route::resource('tickets', TicketController::class);
        Route::post('tickets/{ticket}/close', [\App\Http\Controllers\Admin\TicketController::class, 'markClose'])->name('ticket.mark-closed');


        Route::post('users/{user}/login', AnonymousLoginController::class)->name('users.login');

        Route::post('ajax/get-states', AjaxCallController::class)->name('ajax.state')->withoutMiddleware(['auth']);

        Route::get('language/{locale}', LanguageController::class)->name('locale.change');
});

Route::get('media/get/{document}', function (App\Models\Document $document) {
    if (! Storage::exists($document->getStoragePath())) {
        abort(404, 'Resource Not Found');
    }

    return Storage::response($document->getStoragePath(), $document->name);
})->name('media.get');

Route::get('order/{order}/label/get', function ($order) {
    return response()->download(storage_path("labels/{$order->corrios_tracking_code}.pdf"),$order->corrios_tracking_code,[],'inline');
})->name('order.label.download');