<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\ShCode;
use Illuminate\Http\Request;

class ShCodeController extends Controller
{
    public function __invoke($search)
    {
        return ShCode::query()
                    ->where('code',"LIKE","%{$search}%")
                    ->orWhere('description','LIKE',"%{$search}%")
                    ->get(['code','description']);
    }
}
