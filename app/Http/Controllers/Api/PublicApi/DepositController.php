<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\Deposit\DepositResource;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    public function __invoke(Request $request)
    {
        $startDate = $request->start_date ??date('Y-m-01'); 
        $endDate  = $request->end_date  ??date('Y-m-d'); 
        $deposits = Deposit::with(['orders.tax','user'])->where('user_id',Auth::id())->filter($startDate,$endDate)->get();

        return response()->json([
            'success' =>true,
            'filter' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'deposits' => DepositResource::collection($deposits)
        ]);
    }
}
