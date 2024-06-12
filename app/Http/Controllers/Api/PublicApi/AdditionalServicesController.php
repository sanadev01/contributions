<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\HandlingService;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;

class AdditionalServicesController extends Controller
{
    private $adminId;

    public function __construct()
    {
        $this->adminId = User::ROLE_ADMIN;
    }

    public function __invoke()
    {
        $handlingServices = HandlingService::query()
            ->active()
            ->select('id', 'name', 'price')
            ->get();

        return response()->json($handlingServices);
    }
    
}
