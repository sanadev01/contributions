<?php

namespace App\Services\PaymentServices;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Setting;
use App\Models\PaymentInvoice;
use App\Models\BillingInformation;
use net\authorize\api\contract\v1\OrderType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\CustomerDataType;
use net\authorize\api\contract\v1\CustomerAddressType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\controller\CreateTransactionController;

class AuthorizeNetService
{
    private $merchantAuthentication;
    private $refId;

    public function __construct($id = 1)
    {
        $AuthorizeID = setting('AUTHORIZE_ID', null, null, true);
        $AuthorizeKey = setting('AUTHORIZE_KEY', null, null, true);

        if (! $AuthorizeID || ! $AuthorizeKey) {
            throw new Exception('Athorize Error');
        }

        $this->merchantAuthentication = new MerchantAuthenticationType();
        $this->merchantAuthentication->setName($AuthorizeID);
        $this->merchantAuthentication->setTransactionKey($AuthorizeKey);
        $this->refId = 'Ref_'.date('Ymd').str_random(2);
    }

    public function makeCreditCardPayement(BillingInformation $billingInformation, PaymentInvoice $invoice)
    {
        // Create the payment data for a credit card
        try {
            $creditCard = new CreditCardType();
            $creditCard->setCardNumber(cleanString($billingInformation->card_no));
            $creditCard->setCardCode($billingInformation->cvv);
            $creditCard->setExpirationDate($billingInformation->expiration);

            $paymentOne = new PaymentType();
            $paymentOne->setCreditCard($creditCard);

            $orderType = new OrderType();
            $orderType->setInvoiceNumber($invoice->uuid);
            $orderType->setDescription("An order From Homedeliverybr");

            // Set the customer's Bill To address
            $customerAddress = new CustomerAddressType();
            
            $customerAddress->setFirstName($billingInformation->first_name);
            $customerAddress->setLastName($billingInformation->last_name);
            $customerAddress->setCompany("Homedeliverybr");
            $customerAddress->setAddress($billingInformation->address);
            // $customerAddress->setCity($billingInformation->);
            $customerAddress->setState($billingInformation->state);
            $customerAddress->setZip($billingInformation->zipcode);
            $customerAddress->setCountry($billingInformation->country);

            // Set the customer's identifying information
            $customerData = new CustomerDataType();
            $customerData->setType($invoice->user->account_type);
            $customerData->setId($invoice->user->pobox_number);
            $customerData->setEmail($invoice->user->email);

            // Transaction Request
            $transactionRequestType = new TransactionRequestType();
            $transactionRequestType->setTransactionType('authCaptureTransaction');
            $transactionRequestType->setAmount(
                round($invoice->total_amount, 2)
            );
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setOrder($orderType);

            $request = new CreateTransactionRequest();
            $request->setMerchantAuthentication($this->merchantAuthentication);
            $request->setRefId($this->refId);

            $request->setTransactionRequest($transactionRequestType);
            $controller = new CreateTransactionController($request);
            if ( app()->environment('production') ){
                $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
            }else{
                $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
            }

            \Log::info(
                $invoice
            );

            \Log::info(
                $response->jsonSerialize()
            );

            if ( !$response ){
                return (object)[
                    'success' => false,
                    'data' => null,
                    'message' => 'Error While Creating Payment'
                ];
            }
                
           if ($response->getMessages()->getResultCode() != 'Ok'){
               /**
                 * Faild with unknown error.
                 */
                return (object)[
                    'success' => false,
                    'data' => null,
                    'message' => $response->getMessages()->getMessage()[0]->getText()
                ];
           }

            $tResponse = $response->getTransactionResponse();
            if ( !$tResponse ){
                return (object)[
                    'success' => false,
                    'data' => 'error',
                    'message' => $response->getMessages()->getMessage()[0]->getText()
                ];
            }

            /**
             * Faild with errors.
             */
            if ($tResponse && $tResponse->getErrors() != null) {
                return (object)[
                    'success' => false,
                    'data' => null,
                    'message' => $tResponse->getErrors()[0]->getErrorText()
                ];
            }
            
            if ( $tResponse && $tResponse->getResponseCode() != '1' ){
                throw new Exception(
                    is_object($tResponse->getErrors()) || is_array($tResponse->getErrors()) ? json_encode($tResponse->getErrors()): $tResponse->getErrors()
                );
            }

            return (object)[
                'success' => true,
                'data' => $tResponse,
                'message' => 'Transaction Successfully Made With Transaction ID: '.$tResponse->getTransId()
            ];

        } catch (Exception $ex) {
            return (object)[
                'success' => false,
                'data' => null,
                'message' => $ex->getMessage()
            ];
        }
    }


    public function makeCreditCardPaymentWithoutInvoice(BillingInformation $billingInformation, $uuid, $amount,User $user)
    {
        $invoice = new PaymentInvoice;
        $invoice->total_amount = $amount;
        $invoice->uuid = $uuid;
        $invoice->user = $user;

        return $this->makeCreditCardPayement($billingInformation, $invoice);
    }
}
