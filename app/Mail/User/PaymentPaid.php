<?php

namespace App\Mail\User;

use App\Models\PaymentInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentPaid extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     */
    public function __construct(PaymentInvoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->invoice->user->locale);
        return $this->markdown('emails.user.payment_paid')
            ->bcc(config('hd.email.admin_email'), config('hd.email.admin_name'))
            ->subject('Shipment Paid by User')
            ->to($this->invoice->user);
    }
}
