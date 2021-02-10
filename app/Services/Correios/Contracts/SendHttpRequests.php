<?php 

namespace App\Services\Correios\Contracts;

use App\Services\Correios\Contracts\Container;
use App\Services\Correios\Contracts\Package;

interface SendHttpRequests{

    public function createPackage(Package $package): PackageResponse;

    public function createContainer(Container $container): ContainerResponse;

    public function getCN23(Package $package): CN23Response;

    public function getCN35(Container $container): CN35Response;

    public function getCN38(Container $container): CN38Response;

}