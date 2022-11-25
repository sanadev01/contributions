<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShCode;
use Illuminate\Http\Request;

class ShCodeController extends Controller
{
    public function __invoke($search = null)
    {
        if($search){
            $shCode = ShCode::query()
                        ->where('code',"LIKE","%{$search}%")
                        ->orWhere('description','LIKE',"%{$search}%")
                        ->orderBy('description','ASC')
                        ->get(['code','description']);
            if(!$shCode->isEmpty()){
                return $shCode;
            }

            return apiResponse(false, 'No SH Code Found');
        }
        return ShCode::query()->get(['code','description']);
    }
}
