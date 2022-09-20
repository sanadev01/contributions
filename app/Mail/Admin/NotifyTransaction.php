<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class NotifyTransaction extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $order;
    public $pre_status;
    public $pre_balance;
    public $amount;
    public $rem_balance;
    public $new_status;
    public $created;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $order, $pre_status, $pre_balance, $amount, $rem_balance, $new_status, $created)
    {
        $this->user = $user;
        $this->order = $order;
        $this->pre_status = $pre_status;
        $this->pre_balance = $pre_balance;
        $this->amount = $amount;
        $this->rem_balance = $rem_balance;
        $this->new_status = $new_status;
        $this->created = $created;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.admin.notify-transaction')
        ->to('malikasimit@gmail.com')
            ->subject('Transaction Notification');
    }
}
