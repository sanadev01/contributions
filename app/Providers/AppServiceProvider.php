<?php

namespace App\Providers;


use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\Correios\Services\Brazil\CorreiosBrazilTrackingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->singleton('CorreiosBrazilTracking_service', function() {
            
            $wsdlUrl = env('CORREIOS_WSDL_URL', 'http://webservice.correios.com.br/service/rastro/Rastro.wsdl');
            $user = env('CORREIOS_USER');
            $password = env('CORREIOS_PASSWORD');
            if (!$user || !$password) {
                throw new \Exception("Correios service credentials are not set.");
            }

            return new CorreiosBrazilTrackingService($wsdlUrl, $user, $password);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Schema::defaultStringLength(191);

        Blade::directive('admin', function () {
            return '<?php if ( auth()->user()->isAdmin() ) { ?>';
        });

        Blade::directive('endadmin', function () {
            return '<?php } ?>';
        });

        Blade::directive('user', function () {
            return '<?php if ( auth()->user()->isUser() ) { ?>';
        });

        Blade::directive('enduser', function () {
            return '<?php } ?>';
        });
        // \URL::forceScheme('https');
    }
}
