<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MileExpress\MileExpressService;
use Illuminate\Support\Facades\Http;

class MileExpressServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mileExpress_service', function(){
            $clientId = config('mileExpress.credentials.clientId');
            $clientSecret = config('mileExpress.credentials.clientSecret');
            $userName = config('mileExpress.credentials.userName');
            $password = config('mileExpress.credentials.password');

            $getTokenUrl = config('mileExpress.tokenUrl');
            $houseUrl = config('mileExpress.houseUrl');
            $trackingUrl = config('mileExpress.trackingUrl');
            $createConsolidatorUrl = config('mileExpress.createConsolidatorUrl');
            $registerConsolidatorUrl = config('mileExpress.registerConsolidatorUrl');
            $createMasterUrl = config('mileExpress.createMasterUrl');
            $registerMasterUrl = config('mileExpress.registerMasterUrl');

            return new MileExpressService($clientId, $clientSecret, $userName, $password, $getTokenUrl, $houseUrl, $trackingUrl, $createConsolidatorUrl, $registerConsolidatorUrl, $createMasterUrl, $registerMasterUrl);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('mileExpress', function(){

            $baseUrl = (app()->isProduction()) ? config('mileExpress.production.baseUrl') : config('mileExpress.testing.baseUrl');
            
            return Http::baseUrl($baseUrl);
        });
    }
}
