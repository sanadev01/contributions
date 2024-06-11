<?php 

namespace App\Traits;

trait PrintOrderLabel{

  
    public function renderLabel($request, $order, $error)
    {
        $buttonsOnly = $request->has('buttons_only');

        return view('admin.orders.label.label',compact('order','error' ,'buttonsOnly'));
    }

}