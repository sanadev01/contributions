<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class USPSTrackingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'USPSTracking_service';
    }
}