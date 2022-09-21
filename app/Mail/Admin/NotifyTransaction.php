<?php

namespace App\Mail\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class NotifyTransaction extends Mailable
{
    use Queueable, SerializesModels;
    public $deposit;
    public $preStatus;
    public $user;
    public $newStatus;
    public $order;
    public $orderUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($deposit , $preStatus, $user)
    {
        $this->deposit = $deposit;
        $this->preStatus = $preStatus;
        $this->user = $user;
        
        \Log::info('depositUser');
        \Log::info($deposit->firstOrder);
        $orderUser = User::find($this->deposit->user_id);
        $order = Order::find($this->deposit->order_id);
        $order->refresh();
        \Log::info('orderUser');
        \Log::info($order);
        if($order->status == Order::STATUS_PREALERT_TRANSIT) {
            $newStatus = "STATUS_PREALERT_TRANSIT";
        }elseif($order->status == Order::STATUS_PREALERT_READY){
            $newStatus = "STATUS_PREALERT_READY";
        }elseif($order->status == Order::STATUS_ORDER){
            $newStatus = "STATUS_ORDER";
        }elseif($order->status == Order::STATUS_NEEDS_PROCESSING){
            $newStatus = "STATUS_NEEDS_PROCESSING";
        }elseif($order->status == Order::STATUS_PAYMENT_PENDING){
            $newStatus = "STATUS_PAYMENT_PENDING";
        }elseif($order->status == Order::STATUS_PAYMENT_DONE) {
            $newStatus = "STATUS_PAYMENT_DONE";
        }else{
            $newStatus = 'STATUS_REFUND';
        }
        $this->order = $order;
        $this->orderUser = $orderUser;
        $this->newStatus = $newStatus;

        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(!$this->order){
            $subject = "Account Recharged";
        } else {
            $subject = "Transaction Notification";        }
        return $this->markdown('email.admin.notify-transaction')
        ->to('malikasimit@gmail.com')
            ->subject($subject);
    }
}
