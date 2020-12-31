<?php

namespace App\Models;

use App\Events\OrderPaid;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class PaymentInvoice extends Model
{
    protected $guarded = [];
    const TYPE_PREPAID= 'prepaid';
    const TYPE_POSTPAID= 'postpaid';

    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class,'paid_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class,'invoice_id');
    }

    public function markPaid(bool $paid)
    {
        $this->orders()->update([
            'is_paid' => $paid,
            'status' =>  $paid ? Order::STATUS_PAYMENT_DONE: Order::STATUS_PAYMENT_PENDING
        ]);

        event(new OrderPaid($this->orders, $paid));

        return $this->update([
            'is_paid' => $paid
        ]);
    }

    public function markPrePaid()
    {
        $this->orders()->update([
            'is_paid' => $this->isPaid(),
            'status' =>  $this->isPaid() ? Order::STATUS_PAYMENT_DONE: Order::STATUS_PAYMENT_PENDING
        ]);

        return $this->update([
            'type' => 'prepaid'
        ]);
    }

    public function markPostPaid()
    {
        $this->orders()->update([
            'is_paid' => true,
            'status' => Order::STATUS_PAYMENT_DONE
        ]);

        return $this->update([
            'type' => 'postpaid'
        ]);
    }

    public function isPaid()
    {
        return $this->is_paid;
    }

    public function isPrePaid()
    {
        return $this->type == 'prepaid';
    }

    public static function generateUUID()
    {
        return uniqid('PI-');
    }

    /**
     * Accessors
     */
    public function getTotalAmountAttribute($value)
    {
        return round($value,2);
    }
}
