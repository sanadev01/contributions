<?php

namespace App\Mail\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Mail\Admin\AutoCharge;
use App\Models\BillingInformation;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AutoChargeTransaction extends Mailable
{
    use Queueable, SerializesModels;
    public $deposit;
    public $cardNo;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($deposit)
    {
        $this->deposit = $deposit;
        $this->user = $deposit->user; 
        $billingInfo = BillingInformation::find(setting('charge_biling_information', null, $this->user->id));
        $this->cardNo =  "**** **** **** ".substr($billingInfo->card_no, -4);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      
    return $this->markdown('email.admin.auto-charge-transaction')
        ->to(
           $this->user->email
        )->cc(config('hd.email.admin_email'));
    }
}
