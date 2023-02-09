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
        $from = $request->from ??date('Y-m-01'); 
        $to  = $request->to  ??date('Y-m-d'); 
        $deposits = Deposit::with(['orders.tax','user'])->where('user_id',Auth::id())->filter($from,$to)->get();
        return response()->json([
            'success' =>true,
            'filter' => [
                'from' => $from,
                'to' => $to
            ],
            'deposits' => DepositResource::collection($deposits)
        ]);
    }
}
