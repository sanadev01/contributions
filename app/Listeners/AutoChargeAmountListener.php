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
        $billingInformationId = setting('charge_biling_information', null, auth()->id());
        $billingInformation = $user->billingInformations()->where('id',$billingInformationId)->first();

        if($charge && $user->current_balance < $chargeLimit  && $billingInformation){             
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
                        'description' => 'Auto charged balance',
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
