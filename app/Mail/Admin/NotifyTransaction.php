<?php

namespace App\Mail\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Mail\Admin\AutoCharge;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        
        if(setting('auto_charge', null, $order->user_id) && getBalance($order->user) < 200 ){
            $charge = 200 - getBalance($order->user);
            try {
                \Mail::send(new AutoCharge(round($charge, 2), $order->user));
            } catch (\Exception $ex) {
                \Log::info('Notify Autocharge email send error: '.$ex->getMessage());
            }
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
