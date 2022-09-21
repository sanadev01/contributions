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

        $order = Order::find($this->deposit->order_id);
        if($order) {
            $order->refresh();
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
            }elseif($order->status == Order::STATUS_CANCEL) {
                $newStatus = "STATUS_CANCEL";
            }elseif($order->status == Order::STATUS_REJECTED) {
                $newStatus = "STATUS_REJECTED";
            }elseif($order->status == Order::STATUS_RELEASE) {
                $newStatus = "STATUS_RELEASE";
            }else{
                $newStatus = 'STATUS_REFUND';
            }
            $this->order = $order;
            $this->newStatus = $newStatus;
        }
        

        
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
        ->to(
            config('hd.email.admin_email'),
            config('hd.email.admin_name'),
        )->cc('mnaveedsaim@gmail.com')
        ->subject($subject);
    }
}
