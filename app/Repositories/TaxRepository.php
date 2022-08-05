<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Tax;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use App\Models\User;
use Exception;

class TaxRepository
{

    public function get(Request $request, $paginated = true)
    {
        $query = Tax::has('user');
        if ( $request->search ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->search}%");
            });
            $query->orWhereHas('order',function($query) use($request) {
                return $query->where('warehouse_number', 'LIKE', "%{$request->search}%")
                ->orWhere('corrios_tracking_code', 'LIKE', "%{$request->search}%");
            });
        }
        $query->latest();
            return $query->paginate(50);
        return $query->get();
    }

    public function getOrders(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);
        $trackingNumber = explode(',', preg_replace('/\s+/', '', $request->trackingNumbers));
        return Order::where('user_id',$request->user_id)->whereIn('corrios_tracking_code', $trackingNumber)->get();
    }

    public function store(Request $request)
    {
        $amount = 0;
        try{
            $user = User::find($request->user_id);
            if($user) {
                foreach($request->order_id as $key=> $orderId) {
                    Tax::create([
                        'user_id' => $request->user_id,
                        'order_id' => $orderId,
                        'tax_1' => $request->tax_1[$key],
                        'tax_2' => $request->tax_2[$key],
                    ]);
                    $amount += $request->tax_2[$key];
                }
                $balance = Deposit::getCurrentBalance($user);
                if($balance >= $amount) {
                    Deposit::create([
                        'uuid' => PaymentInvoice::generateUUID('DP-'),
                        'amount' => $amount,
                        'user_id' => $request->user_id,
                        'balance' => $balance - $amount,
                        'is_credit' => false,
                        'last_four_digits' => 'Pay Tax',
                        'description' => "Pay Tax",
                    ]);
                    return true;
                }
                return false;
            }
        }catch(Exception $exception){
            session()->flash('alert-danger','Error while Adding Tax'. $exception->getMessage());
            return null;
        }
    }

    public function update(Request $request)
    {
        //
    }


    public function delete()
    {
        //
    }

}
