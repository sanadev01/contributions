<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CorreosChile\CorreosChileService;
use App\Services\CorreosChile\CorreosChileTrackingService;

class ChileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('CorreosChile_service', function() {

            // Chile Api Credentials Production environment
            $createShipmentUrl = config('correoschile.production.createShipmentUrl');
            $addressValidationUrl = config('correoschile.production.normalize_address_url');
            $getRegionsUrl = config('correoschile.production.regions_url');
            $getCommunesUrl = config('correoschile.production.communas_url');
            $usuario = config('correoschile.production.userId');              //CorreosChile user Id
            $contrasena = config('correoschile.production.correosKey');  //CorreosChile Key
            $codigoAdmision = config('correoschile.production.transactionId');   // ID transaction (Internal Client), with this data the XML Response is returned.
            $clienteRemitente = config('correoschile.production.codeId');       // ID Code SAP Customer. Delivered by CorreosChile

            return new CorreosChileService($createShipmentUrl, $addressValidationUrl, $getRegionsUrl, $getCommunesUrl, $usuario, $contrasena, $codigoAdmision, $clienteRemitente);
        });

        $this->app->singleton('CorreosChileTracking_service', function() {

            // Api Credentials
            $apiUrl = 'https://wsseguimientoclientes.correos.cl:10443/api/v1/appmovil/';
            $user = 'userappmobile';
            $password = 'dXNlcm1vYmlsZUNvcnJlb3MyMDIx';
            return new CorreosChileTrackingService($apiUrl, $user, $password);
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
