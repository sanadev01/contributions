<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Warehouse\ContainerRepository;

class AwbController extends Controller
{
    public function __invoke(Request $request, ContainerRepository $containerRepository)
    {
        $request->validate([
            'awb' => 'required',
        ]);
        $response = $containerRepository->updateawb($request);
        return back()->withInput();
    }
}
