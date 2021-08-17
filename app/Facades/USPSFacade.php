<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class USPSFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'USPS_service';
    }
}
