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

            $clientId = config('fedex.credentials.client_id');
            $clientSecret = config('fedex.credentials.client_secret');
            $accountNumber = config('fedex.credentials.account_number'); //109740748 // 109740748

            // FedEx Api Testing Environment Credentials
            $getTokenUrl = config('fedex.testing.getTokenUrl');
            $getRatesUrl = config('fedex.testing.getRatesUrl');
            $createShipmentUrl = config('fedex.testing.createShipmentUrl');
           
            return new FedExService($clientId, $clientSecret, $accountNumber, $getTokenUrl, $getRatesUrl, $createShipmentUrl);
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
