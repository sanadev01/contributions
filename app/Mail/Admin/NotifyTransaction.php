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
            $this->order = $order;
            $this->newStatus = $order->status_name;
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
            $subject = "Transaction Update";
        } else {
            $subject = "Transaction Notification";        
        }
        return $this->markdown('email.admin.notify-transaction')
        ->to('mnaveedsaim@gmail.com')
        ->subject($subject);
    }
}
