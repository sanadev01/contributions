<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicApi\UserShippingResource;
use App\Models\ShippingService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfitSetting;

class UserServicesController extends Controller
{

    public function __invoke()
    {
        $settings = ProfitSetting::where('user_id', Auth::id())->get();
        return response()->json([
            'data' => UserShippingResource::collection($settings),
        ], 200);
    }
}