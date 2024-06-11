<?php

namespace App\Mail\Admin;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PreAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $message;
    public $name;
    public $poBox;
    public $codes;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $order, $name, $poBox)
    {
        $this->order = $order;
        $this->message = $message;
        $this->name = $name;
        $this->poBox = $poBox;
        $this->codes = null;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (count($this->order) >= 1) { $orders = $this->order->pluck('corrios_tracking_code')->toArray(); }
        $this->codes = implode(", ",$orders);
        return $this->markdown('emails.admin.pre-alert')->to(config('hd.email.admin_email'))->subject('Pre Alert HD-BR');
    }
}
