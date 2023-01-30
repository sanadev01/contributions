<?php

namespace App\Listeners;

use App\Events\AutoChargeAmountEvent;
use App\Mail\Admin\NotifyTransaction;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use App\Services\PaymentServices\AuthorizeNetService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoChargeAmountListener
{
    public $user;
    public $autoCharge;
    public $authoChargeLimit;
    public $amount;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    { 
    }

    /**
     * Handle the event.
     *
     * @param  AutoChargeAmountEvent  $event
     * @return void
     */
    public function handle(AutoChargeAmountEvent $event)
    {
        $this->user = $event->user; 
        
        $this->autoCharge =  setting('auto_charge', null, $this->user->id);
        $this->authoChargeLimit = setting('auto_charge_limit', null, $this->user->id);
        $this->amount = setting('auto_charge_amount', null, $this->user->id);
        $billingInformation = $this->user->billingInformations()->latest()->first();

        if($this->autoCharge && $this->user->current_balance < $this->authoChargeLimit  && $billingInformation){ 
             
            $authorizeNetService = new AuthorizeNetService(); 
            $transactionID = PaymentInvoice::generateUUID('DP-');
            $response = $authorizeNetService->makeCreditCardPaymentWithoutInvoice($billingInformation,$transactionID,$this->amount,$this->user);
       
            if ($response->success){
 
                    $deposit = Deposit::create([
                        'uuid' => $transactionID,
                        'transaction_id' => $response->data->getTransId(),
                        'amount' => $this->amount,
                        'user_id' => Auth::id(),
                        'balance' => Deposit::getCurrentBalance() + $this->amount,
                        'is_credit' => true,
                        'last_four_digits' => substr($billingInformation->card_no,-4)
                    ]); 
                $this->sendTransactionMail($deposit, $this->user->name);
            }   
        }
        
    }

    private function sendTransactionMail($deposit, $user){
        try {
            Mail::send(new NotifyTransaction($deposit, null, $user));
        } catch (Exception $ex) {
            Log::info('Auto charge Notify Transaction email send error: '.$ex->getMessage());
        }
    }

}
