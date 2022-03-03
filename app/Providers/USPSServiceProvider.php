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

            // USPS Api Production Environment Credentials
            
            // $api_url = config('usps.production.url');
            // $delete_usps_label_url = config('usps.production.delete_label_url');
            // $create_manifest_url = config('usps.production.create_manifest_url');
            // $get_price_url = config('usps.production.get_price_url');
            // $email = config('usps.production.email');           
            // $password = config('usps.production.password');

            // USPS Api Testing Environemtn Credentials
            $createLabelUrl = config('usps.testing.createLabelUrl');
            $deleteLabelUrl = config('usps.testing.deleteLabelUrl');
            $createManifestUrl = config('usps.testing.createManifestUrl');
            $getPriceUrl = config('usps.testing.getPriceUrl');
            $addressValidationUrl = config('usps.testing.addressValidationUrl');
            $email = config('usps.testing.email');           
            $password = config('usps.testing.password');

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
