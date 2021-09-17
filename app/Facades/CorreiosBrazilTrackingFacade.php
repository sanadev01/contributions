<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CorreiosBrazilTrackingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'CorreiosBrazilTracking_service';
    }
}