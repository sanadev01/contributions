<?php

namespace App\Http\Controllers\Warehouse;

use Illuminate\Http\Request;
use App\Models\Warehouse\Container;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AwbController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'awb' => 'required',
        ]);

        if(json_decode($request->data)){
            foreach(json_decode($request->data) as $containerId){
                $container       = Container::find($containerId);
                $container->awb  = $request->awb;
                $container->save();
            }
            session()->flash('alert-success','AWB number assigned');
            return redirect()->back();
        }
        session()->flash('alert-danger','Please select Containers');
        return redirect()->back();

    }
}
