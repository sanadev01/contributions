<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function __invoke()
    {
        return apiResponse(true,'Your Balance in USD',Deposit::getCurrentBalance());
    }
}
