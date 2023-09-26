<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class POSTNLServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('POSTNL_service', function() {

            if (app()->isProduction()) {
                // POSTNL Api Production Environment Credentials
                $createLabelUrl = config('postnl.production.createLabelUrl');
                $cancelLabelUrl = config('postnl.production.deleteLabelUrl');
            }else {

                // POSTNL Api Testing Environemtn Credentials
                $createLabelUrl = config('postnl.testing.createLabelUrl');
                $cancelLabelUrl = config('postnl.testing.deleteLabelUrl');
            }


            return [$createLabelUrl, $cancelLabelUrl];
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
