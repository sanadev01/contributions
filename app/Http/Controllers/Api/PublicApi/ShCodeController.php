<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShCode;
use Illuminate\Http\Request;

class ShCodeController extends Controller
{
    public function __invoke($search = null)
    {
        $type = strtolower(request()->type) == "courier"  ? 'total' : null; 
        $shCode = ShCode::where('type', $type);
        if ($search) {
            $shCode = $shCode->where('code', "LIKE", "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%");
        }
        $shCode = $shCode->orderBy('description', 'ASC')->get(['code', 'description']); 
        if (!$shCode->isEmpty()) {
            $shCodes = array();
            foreach ($shCode as $sh) {
                array_push($shCodes, [
                    'code' => $sh->code,
                    'type' =>  request()->type == 'courier' ?'Courier':'Postal (Correios)',
                    'description' => $sh->description,
                ]);
            }
            return $shCodes;
        }
        else{
            return apiResponse(false, 'No SH Code Found');
        }
    }
}
