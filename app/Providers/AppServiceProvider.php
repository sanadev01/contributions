<?php

namespace App\Providers;


use App\Services\UPS\UpsService;
use App\Services\USPS\UspsService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Services\USPS\USPSTrackingService;
use App\Services\CorreosChile\CorreosChileService;
use App\Services\CorreosChile\CorreosChileTrackingService;
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
        $this->app->singleton('CorreosChile_service', function() {

            // Chile Api Credentials Production environment

            $wsdlUrl = config('correoschile.url');
            $usuario = config('correoschile.userId');              //CorreosChile user Id
            $contrasena = config('correoschile.correosKey');  //CorreosChile Key
            $codigoAdmision = config('correoschile.transactionId');   // ID transaction (Internal Client), with this data the XML Response is returned.
            $clienteRemitente = config('correoschile.codeId');       // ID Code SAP Customer. Delivered by CorreosChile

            // Chile Api Credentials Test environment

            // $wsdlUrl = 'http://apicert.correos.cl:8008/ServicioAdmisionCEPExterno/cch/ws/enviosCEP/externo/implementacion/ServicioExternoAdmisionCEP.asmx?WSDL';
            // $usuario = 'PRUEBA WS 1';
            // $contrasena = 'b9d591ae8ef9d36bb7d4e18438d6114e';
            // $codigoAdmision = 'PRB20201103';
            // $clienteRemitente = '61001';

            return new CorreosChileService($wsdlUrl, $usuario, $contrasena, $codigoAdmision, $clienteRemitente);
        });

        $this->app->singleton('USPS_service', function() {
            // USPS Api Testing Environemtn Credentials
            $api_url = 'https://api-sandbox.myibservices.com/v1/labels';
            $delete_usps_label_url = 'https://api-sandbox.myibservices.com/v1/labels/';
            $create_manifest_url = 'https://api-sandbox.myibservices.com/v1/manifests.json';
            $get_price_url = 'https://api-sandbox.myibservices.com/v1/price.json';
            $email = 'ghaziislam3@gmail.com';           
            $password = 'Ikonic@1234';

            // USPS Api Production Environment Credentials
            // $api_url = config('usps.url');
            // $delete_usps_label_url = config('usps.delete_label_url');
            // $create_manifest_url = config('usps.create_manifest_url');
            // $get_price_url = config('usps.get_price_url');
            // $email = config('usps.email');           
            // $password = config('usps.password');

            return new UspsService($api_url, $delete_usps_label_url, $create_manifest_url, $get_price_url, $email, $password);
        });

        $this->app->singleton('UPS_service', function() {
            // USPS Api Testing Environemtn Credentials
            $create_package_url = 'https://wwwcie.ups.com/ship/v1/shipments';
            $delete_package_url = '';
            $create_manifest_url = '';
            $rating_package_url = 'https://onlinetools.ups.com/ship/v1/rating/Rate';
            $transactionSrc = 'HERCO';
            $userName = 'hffinc1';           
            $password = 'Hdbrasilc4!';
            $shipperNumber = '022VX0';

            // USPS Api Production Environment Credentials
            // $api_url = config('usps.url');
            // $delete_usps_label_url = config('usps.delete_label_url');
            // $create_manifest_url = config('usps.create_manifest_url');
            // $get_price_url = config('usps.get_price_url');
            // $email = config('usps.email');           
            // $password = config('usps.password');

            return new UpsService($create_package_url, $delete_package_url, $create_manifest_url, $rating_package_url, $transactionSrc, $userName, $password, $shipperNumber);
        });

        $this->app->singleton('CorreiosBrazilTracking_service', function() {

            // Api Credentials
            $wsdlUrl = 'http://webservice.correios.com.br/service/rastro/Rastro.wsdl';
            $user = '9912501576';
            $password = 'N>WTBF@3GP';
            return new CorreiosBrazilTrackingService($wsdlUrl, $user, $password);
        });

        $this->app->singleton('CorreosChileTracking_service', function() {

            // Api Credentials
            $apiUrl = 'https://wsseguimientoclientes.correos.cl:10443/api/v1/appmovil/';
            $user = 'userappmobile';
            $password = 'dXNlcm1vYmlsZUNvcnJlb3MyMDIx';
            return new CorreosChileTrackingService($apiUrl, $user, $password);
        });

        $this->app->singleton('USPSTracking_service', function() {
            // USPS Api Testing Environemtn Credentials
            // $api_url = 'https://api-sandbox.myibservices.com/v1/track/';
            // $email = 'ghaziislam3@gmail.com';           
            // $password = 'Ikonic@1234';

            // Usps Tracking Api Credentials Production Enironment
            $apiUrl = config('usps.tracking_url');
            $email = config('usps.email');           
            $password = config('usps.password');
            return new USPSTrackingService($apiUrl, $email, $password);
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
