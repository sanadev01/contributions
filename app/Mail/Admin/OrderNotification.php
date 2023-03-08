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

class OrderNotification extends Mailable
{
    use Queueable, SerializesModels;
    public $preStatus;
    public $user;
    public $newStatus;
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order , $preStatus, $user)
    {
        $this->order = $order;
        $this->preStatus = $preStatus;
        $this->user = $user;

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
        $subject = "Order Notification";    
        return $this->markdown('email.admin.order-notification')
        ->to(
            config('hd.email.admin_email'),
            config('hd.email.admin_name'),
        )->cc('mnaveedsaim@gmail.com')
        ->subject($subject);
    }
}
