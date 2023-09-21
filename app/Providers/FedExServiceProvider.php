<?php

namespace App\Providers;

use App\Services\FedEx\FedExService;
use Illuminate\Support\ServiceProvider;

class FedExServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('fedex_service', function() {

            if (app()->isProduction()) {

                $clientId = config('fedex.production.credentials.client_id');
                $clientSecret = config('fedex.production.credentials.client_secret');
                $accountNumber = config('fedex.production.credentials.account_number');

                $getTokenUrl = config('fedex.production.getTokenUrl');
                $getRatesUrl = config('fedex.production.getRatesUrl');
                $createShipmentUrl = config('fedex.production.createShipmentUrl');
                $createPickupUrl = config('fedex.production.createPickupUrl');
                $cancelPickupUrl = config('fedex.production.cancelPickupUrl');

            }else {

                $clientId = config('fedex.testing.credentials.client_id');
                $clientSecret = config('fedex.testing.credentials.client_secret');
                $accountNumber = config('fedex.testing.credentials.account_number');

                $getTokenUrl = config('fedex.testing.getTokenUrl');
                $getRatesUrl = config('fedex.testing.getRatesUrl');
                $createShipmentUrl = config('fedex.testing.createShipmentUrl');
                $createPickupUrl = config('fedex.testing.createPickupUrl');
                $cancelPickupUrl = config('fedex.testing.cancelPickupUrl');
            }
           
            return new FedExService($clientId, $clientSecret, $accountNumber, $getTokenUrl, $getRatesUrl, $createShipmentUrl, $createPickupUrl, $cancelPickupUrl);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
