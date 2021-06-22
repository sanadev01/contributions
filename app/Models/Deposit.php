<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getCurrentBalance($user=null)
    {
        $lastTransaction = self::query()->where('user_id',$user ? $user->id:  Auth::id())->latest('id')->first();
        if ( !$lastTransaction ){
            return 0;
        }

        return $lastTransaction->balance;
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function firstOrder()
    {
        return $this->orders()->first();
    }

    public function hasOrder()
    {
        return $this->orders()->count();
    }

    public static function chargeAmount($amount,Order $order=null)
    {

        $deposit = self::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $amount,
            'user_id' => Auth::id(),
            'balance' => Deposit::getCurrentBalance() - $amount,
            'is_credit' => false,
        ]);

        if ( $order ){
            $order->deposits()->sync($deposit->id);
        }

        return $deposit;
        
    }


    public function isCredit()
    {
        return $this->is_credit;
    }
}
