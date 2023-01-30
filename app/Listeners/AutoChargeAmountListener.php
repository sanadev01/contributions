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
        $user =  $event->user; 
        $autoCharge  = setting('auto_charge', null, $user->id);
        $authoChargeLimit = setting('auto_charge_limit', null, $user->id);
        $amount = setting('auto_charge_amount', null, $user->id);

        $billingInformation = $user->billingInformations()->latest()->first();

        if($autoCharge && $user->current_balance < $authoChargeLimit  && $billingInformation){ 
             
            $authorizeNetService = new AuthorizeNetService(); 
            $transactionID = PaymentInvoice::generateUUID('DP-');
            $response = $authorizeNetService->makeCreditCardPaymentWithoutInvoice($billingInformation,$transactionID,$amount,$user);
       
            if ($response->success){
 
                    $deposit = Deposit::create([
                        'uuid' => $transactionID,
                        'transaction_id' => $response->data->getTransId(),
                        'amount' => $amount,
                        'user_id' => Auth::id(),
                        'balance' => Deposit::getCurrentBalance() + $amount,
                        'is_credit' => true,
                        'last_four_digits' => substr($billingInformation->card_no,-4)
                    ]); 
                $this->sendTransactionMail($deposit, $user->name);
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
