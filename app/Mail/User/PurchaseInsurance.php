<?php

namespace App\Mail\User;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseInsurance extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     *
     * @param PreAlert $preAlert
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
        app()->setLocale($this->order->user->locale);
        $subject = "Insurance Purchased - Warehouse Number: " . $this->order->warehouse_number;
        return $this->markdown('emails.user.insurance-purchased')
                ->subject($subject)
                ->to('invoicing@hercoinc.com');
    }
}
