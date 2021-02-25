<?php


namespace App\Repositories;


use App\Events\OrderPaid;
use App\Mail\User\PaymentPaid;
use App\Models\BillingInformation;
use App\Models\Country;
use App\Models\Order;
use App\Models\State;
use App\Services\PaymentServices\AuthorizeNetService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepositRepository
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $billingInformation = null;

            if ( $request->billingInfo ){
                $billingInformation = BillingInformation::find($request->billingInfo);
            }

            if ( !$billingInformation ){
                $billingInformation = new BillingInformation([
                    'user_id' => Auth::id(),
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'card_no' => $request->card_no,
                    'expiration' => $request->expiration,
                    'cvv' => $request->cvv,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'state' => State::find($request->state)->code,
                    'zipcode' => $request->zipcode,
                    'country' => Country::find($request->country)->name
                ]);
            }

            if ( $request->has('save-address') ){
                $billingInformation->save();
            }

            $authorizeNetService = new AuthorizeNetService();

            $response = $authorizeNetService->makeCreditCardPayement($billingInformation,$paymentInvoice);


            if ( !$response->success ){
                $this->error = json_encode($response->message);
                DB::rollBack();
                return false;
            }

            $paymentInvoice->update([
                'last_four_digits' => substr($billingInformation->card_no,-4),
                'is_paid' => true
            ]);

            $paymentInvoice->transactions()->create([
                'transaction_id' => $response->data->getTransId(),
                'amount' => $paymentInvoice->total_amount
            ]);

            $paymentInvoice->orders()->update([
                'is_paid' => true,
                'status' => Order::STATUS_PAYMENT_DONE
            ]);

            event(new OrderPaid($paymentInvoice->orders, true));

            try {
                \Mail::send(new PaymentPaid($paymentInvoice));
            } catch (\Exception $ex) {
                \Log::info('Payment Paid email send error: '.$ex->getMessage());
            }

            DB::commit();

            return true;

        } catch (\Exception $ex) {
            DB::rollBack();
            $this->error = $ex->getMessage();
            return false;
        }
    }

}
