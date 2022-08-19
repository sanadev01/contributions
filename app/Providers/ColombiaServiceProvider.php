<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Colombia\ColombiaService;

class ColombiaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
    */

    public function register()
    {
        $this->app->singleton('colombia_service', function() {
            if (app()->isProduction()) {
                $userName = config('credentials.credentials.username');
                $password = config('ColombiaService.credentials.password');
                $contractCode = config('ColombiaService.credentials.contractCode');
                $headquarterCode = config('ColombiaService.credentials.headquarterCode');
                $shippingUrl = config('ColombiaService.production.shippingUrl');

            }else{
                $userName = config('ColombiaService.credentials.username');
                $password = config('ColombiaService.credentials.password');
                $contractCode = config('ColombiaService.credentials.contractCode');
                $headquarterCode = config('ColombiaService.credentials.headquarterCode');
                $shippingUrl = config('ColombiaService.testing.shippingUrl');
            }

            return new ColombiaService($userName, $password, $contractCode, $headquarterCode, $shippingUrl);
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
