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


// Route::get('/', function () {
//     return redirect('login');
// });
Auth::routes();

Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class,'logout']);


Route::namespace('Admin')
    ->middleware(['auth'])
    ->as('admin.')
    ->group(function () {

        Route::get('dashboard', 'HomeController')->name('home');
        Route::resource('roles', RoleController::class);
        Route::resource('roles.permissions', RolePermissionController::class);

        Route::resource('roles', RoleController::class);
        Route::resource('roles.permissions', RolePermissionController::class);

        Route::resource('services', HandlingServiceController::class)->except('show');
        Route::resource('addresses', AddressController::class);
        Route::resource('shipping-services', ShippingServiceController::class);


        Route::prefix('rates')
            ->namespace('Rates')
            ->as('rates.')
            ->group(function () {
                Route::resource('profit-packages', ProfitPackageController::class); 
                Route::resource('bps-leve', RateController::class)->only(['create', 'store', 'index']);
            });

    Route::resource('settings', SettingController::class)->only(['index', 'store']);
    Route::resource('profile', ProfileController::class)->only(['index', 'store']);
    Route::resource('users', UserController::class);

    Route::resource('users/export', UserExportController::class)->only('index')->names([
        'index' => 'users.export.index'
    ]);

    Route::post('users/{user}/login', AnonymousLoginController::class)->name('users.login');

});