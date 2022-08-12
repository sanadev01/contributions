<?php

namespace App\Http\Controllers\Admin\Order;

use App\Models\User;
use App\Models\Order;
use App\Mail\Admin\PreAlert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Repositories\OrderRepository;


class PreAlertMailController extends Controller
{
    public function __invoke(Request $request)
    {
        $orderIds = json_decode($request->get('data'),true);

        if (!$orderIds) {
            session()->flash('alert-danger', 'orders must be selected');
            return back();
        }

        $query = Order::query();
        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }
        $orders = $query->whereIn('id',$orderIds)->get();

        if ($orders->isEmpty()) {
            session()->flash('alert-danger', 'Selected orders does not have labels');
            return back();
        }

        if($orders) {
            
            try {
                
                Mail::send(new PreAlert($request->message, $orders));
                session()->flash('alert-success', 'Pre Alert Email Sent Successfully!');
                return back();
            } catch (\Exception $ex) {
                \Log::info('Pre Alert email error: '.$ex->getMessage());
            }
        }
        
        return back();
    }
}
