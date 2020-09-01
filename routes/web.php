<?php

use Illuminate\Support\Facades\Route;

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
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

Route::namespace('Admin')
    ->as('admin.')
    ->prefix('admin')->group(function () {

    Route::resource('prealerts', PreAlertController::class);

    Route::resource('roles', RoleController::class);
    Route::resource('roles.permissions', RolePermissionController::class);

    Route::resource('services', HandlingServiceController::class)->except('show');
    Route::resource('addresses', AddressController::class);
    Route::resource('shipping-services', ShippingServiceController::class);

    Route::resource('tickets', TicketController::class);                
    Route::post('tickets/{ticket}/close', [\App\Http\Controllers\Admin\TicketController::class, 'markClose'])->name('ticket.mark-closed');

    Route::prefix('rates')
    ->namespace('Rates')
    ->as('rates.')
    ->group(function () {
        Route::resource('profit-packages', ProfitPackageController::class); 
        Route::resource('bps-leve', RateController::class)->only(['create', 'store', 'index']);
    });

    Route::resource('settings', SettingController::class)->only(['index', 'store']);
    Route::resource('profile', ProfileController::class)->only(['index', 'store']);


});