<?php

namespace App\Providers;

use App\Services\USPS\UspsService;
use Illuminate\Support\ServiceProvider;
use App\Services\USPS\USPSTrackingService;

class USPSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('USPS_service', function() {
            
            if (app()->isProduction()) {
                // USPS Api Production Environment Credentials
                $createLabelUrl = config('usps.production.createLabelUrl');
                $deleteLabelUrl = config('usps.production.deleteLabelUrl');
                $createManifestUrl = config('usps.production.createManifestUrl');
                $getPriceUrl = config('usps.production.getPriceUrl');
                $addressValidationUrl = config('usps.production.addressValidationUrl');
                $email = config('usps.production.email');           
                $password = config('usps.production.password');
                
            }else {
                
                // USPS Api Testing Environemtn Credentials
                $createLabelUrl = config('usps.testing.createLabelUrl');
                $deleteLabelUrl = config('usps.testing.deleteLabelUrl');
                $createManifestUrl = config('usps.testing.createManifestUrl');
                $getPriceUrl = config('usps.testing.getPriceUrl');
                $addressValidationUrl = config('usps.testing.addressValidationUrl');
                $email = config('usps.testing.email');           
                $password = config('usps.testing.password');
            }

            

            return new UspsService($createLabelUrl, $deleteLabelUrl, $createManifestUrl, $getPriceUrl, $addressValidationUrl, $email, $password);
        });

        $this->app->singleton('USPSTracking_service', function() {

            // Usps Tracking Api Credentials Production Enironment
            $apiUrl = config('usps.production.trackingUrl');
            $email = config('usps.production.email');           
            $password = config('usps.production.password');
            return new USPSTrackingService($apiUrl, $email, $password);
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
