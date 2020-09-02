<?php

namespace App\Mail\User;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentPaid extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->order->user->preferredLocale());
        return $this->markdown('emails.user.payment_paid')
            ->bcc(config('hd.email.admin_email'), config('hd.email.admin_name'))
            ->subject('Shipment Paid by User')
            ->to($this->order->user);
    }
}
