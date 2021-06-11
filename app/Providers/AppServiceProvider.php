<?php

namespace App\Providers;


use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\CorreosChile\CorreosChileService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('CorreosChile_service', function() {

            // Api Credentials(currently credentials of testing environment are beign used)
            $wsdlUrl = config('correoschile.url');
            // $usuario = config('correoschile.userId');              //CorreosChile user Id
            $usuario = 'PRUEBA WS 1';
            $contrasena = config('correoschile.correosKey');  //CorreosChile Key
            $codigoAdmision = config('correoschile.transactionId');   // ID transaction (Internal Client), with this data the XML Response is returned.
            $clienteRemitente = config('correoschile.codeId');       // ID Code SAP Customer. Delivered by CorreosChile

            return new CorreosChileService($wsdlUrl, $usuario, $contrasena, $codigoAdmision, $clienteRemitente);
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
