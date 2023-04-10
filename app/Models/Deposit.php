<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
class Deposit extends Model
{
    use LogsActivity;
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
                            ->logAll()
                            ->logOnlyDirty()
                            ->dontSubmitEmptyLogs();
    }
    
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

    public function depositAttchs()
    {
        return $this->belongsToMany(Document::class);
    }

    public function firstOrder()
    {
        return $this->orders()->first();
    }

    public function hasOrder()
    {
        return $this->orders()->count();
    }

    public static function chargeAmount($amount,Order $order=null,$description=null)
    {

        $deposit = self::create([
            'uuid' => PaymentInvoice::generateUUID('DP-'),
            'amount' => $amount,
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'balance' => Deposit::getCurrentBalance() - $amount,
            'is_credit' => false,
            'description' => $description,
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
    public function getTypeAttribute(){
        return $this->is_credit?'Credit':'Debit';
    }

    public function getOrder($orderId)
    {
        return Order::find($orderId);
    }

    public static function getLiabilityBalance($user=null)
    {
        $totalBalance= self::query()->sum('balance');
        if ( !$totalBalance ){
            return 0;
        }

        return $totalBalance;
    }
    public function scopeFilter($query,$startDate,$endDate)
    {
        $query->when($startDate && $endDate,function($query) use ($startDate,$endDate){ 
            return $query->whereBetween('created_at' , [$startDate.' 00:00:00', $endDate.' 23:59:59']);
        });
    }
}
