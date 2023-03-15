<?php

namespace App\Listeners;

use App\Events\AutoChargeAmountEvent;
use App\Mail\Admin\AutoChargeTransaction;
use App\Models\Deposit;
use App\Models\PaymentInvoice;
use App\Services\PaymentServices\AuthorizeNetService;
use Exception;
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
        $charge  = setting('charge', null, $user->id);
        $chargeLimit = setting('charge_limit', null, $user->id);
        $amount = setting('charge_amount', null, $user->id);
        $billingInformationId = setting('charge_biling_information', null,$user->id);
        $billingInformation = $user->billingInformations()->where('id',$billingInformationId)->first();
        
        if($charge && Deposit::getCurrentBalance($user) < $chargeLimit  && $billingInformation){         
            $authorizeNetService = new AuthorizeNetService(); 
            $transactionID = PaymentInvoice::generateUUID('DP-');
            $response = $authorizeNetService->makeCreditCardPaymentWithoutInvoice($billingInformation,$transactionID,$amount,$user);
            if ($response->success){
                    $deposit = Deposit::create([
                        'uuid' => $transactionID,
                        'transaction_id' => $response->data->getTransId(),
                        'amount' => $amount,
                        'user_id' => $user->id,
                        'balance' => Deposit::getCurrentBalance($user) + $amount,
                        'is_credit' => true,
                        'description' => 'Auto charged balance',
                        'last_four_digits' => substr($billingInformation->card_no,-4)
                    ]); 
                $this->sendTransactionMail($deposit);
            }   
        }
        
    }
    private function sendTransactionMail($deposit){
        try {
            Mail::send(new AutoChargeTransaction($deposit));
        } catch (Exception $ex) {
            Log::info('NO#1 : Auto Charge Transaction email send error  '.$ex->getMessage());
        }
    }

}