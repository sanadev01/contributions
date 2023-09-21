<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UPSFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'UPS_service';
    }
}
