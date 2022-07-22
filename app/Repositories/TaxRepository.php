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
    public function get(Request $request)
    {
        $taxlist = Tax::query();
        return $taxlist;
    }
    {
        return Tax::all();
    }

    public function getOrders(Request $request)
    {
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
