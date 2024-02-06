<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShCode;
use Illuminate\Http\Request;

class ShCodeController extends Controller
{
    public function __invoke($search = null)
    {
        if ($search) {
            $type = strtolower(request()->type) == "courier"  ? 'total' : null;
            $type = strtolower(request()->type) == "postal" ? 'postal' : $type;
            $shCode = ShCode::query()
                ->where('type', $type)
                ->where('code', "LIKE", "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orderBy('description', 'ASC')
                ->get(['code', 'description']);
            if ($shCode->isEmpty()) {  
                return apiResponse(false, 'No SH Code Found');
            }
        }
        else{
            $shCode = ShCode::query()->get(['code', 'description']);
        }
        if (!$shCode->isEmpty()) {
            $shCodes = array();
            foreach ($shCode as $sh) {
                array_push($shCodes, [
                    'code' => $sh->code,
                    'type' =>  request()->type  ?? 'Corroies',
                    'description' => $sh->description,
                ]);
            }
            return $shCodes;
        }
    }
}
