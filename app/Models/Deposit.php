<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class Deposit extends Model
{
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
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

    public function tax()
    {
        return $this->hasOne(Tax::class);
    }
    
    public function getIsTaxAttribute()
    {
        return $this->tax!=null;
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
        if($user){
            $totalBalance= self::query()->where('user_id',$user ? $user->id:  Auth::id())->groupBy('user_id')->orderBy('created_at', 'desc')->sum('balance');
        }
        $totalBalance= self::query()->groupBy('user_id')->orderBy('created_at', 'desc')->sum('balance');
        if ( !$totalBalance ){
            return 0;
        }

        return $totalBalance;
    }
    public function scopeFilter($query,$from=null,$to=null)
    {
        
        $from = Request::get('from')??$from; 
        $to  = Request::get('to')??$to;
        
        $query->when($from,function($query,$from){ 
            return $query->where('created_at' , '>=',$from.' 00:00:00');
        })->when($to,function($query,$to){
              $endDate = $to.' 23:59:59';
           return $query->where('created_at' , '<=',$endDate);
        });
    }
}
