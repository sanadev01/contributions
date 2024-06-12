<?php

namespace App\Providers;

use App\Services\UPS\UpsService;
use Illuminate\Support\ServiceProvider;

class UPSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('UPS_service', function() {
            // UPS Api Testing Environemtn Credentials
            $createPackageUrl = config('ups.testing.createPackageUrl');
            $deletePackageUrl = config('ups.testing.deletePackageUrl');
            $createManifestUrl = config('ups.testing.createManifestUrl');
            $ratingPackageUrl = config('ups.testing.ratingPackageUrl');
            $pickupRatingUrl = config('ups.testing.pickupRatingUrl');
            $pickupShipmentUrl = config('ups.testing.pickupShipmentUrl');
            $pickupCancelUrl = config('ups.testing.pickupCancelUrl');
            $trackingUrl = config('ups.testing.trackingUrl');
            $transactionSrc = config('ups.testing.transactionSrc');
            $userName = config('ups.testing.userName');     
            $password = config('ups.testing.password');
            $shipperNumber = config('ups.testing.shipperNumber');
            $AccessLicenseNumber = config('ups.testing.AccessLicenseNumber');

            return new UpsService($createPackageUrl, $deletePackageUrl, $createManifestUrl, $ratingPackageUrl, $pickupRatingUrl, $pickupShipmentUrl, $pickupCancelUrl, $trackingUrl, $transactionSrc, $userName, $password, $shipperNumber, $AccessLicenseNumber);
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
