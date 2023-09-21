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

        // \Log::info('Authorize ID: ' . $AuthorizeID);
        // \Log::info('Authorize Key: ' . $AuthorizeKey);
        
        if (! $AuthorizeID || ! $AuthorizeKey) {
            throw new Exception('Athorize Error');
        }

        $this->merchantAuthentication = new MerchantAuthenticationType();
        $this->merchantAuthentication->setName($AuthorizeID);
        $this->merchantAuthentication->setTransactionKey($AuthorizeKey);
        $this->refId = 'Ref_'.date('Ymd').str_random(2);

        \Log::info('MerchantAuthenticationType: ');
        \Log::info(json_encode($this->merchantAuthentication));
    }

    public function makeCreditCardPayement(BillingInformation $billingInformation, PaymentInvoice $invoice)
    {
        \Log::info('billingInformation: ');
        \Log::info(json_encode($billingInformation));

        \Log::info('PaymentInvoice: ');
        \Log::info(json_encode($invoice));

        // Create the payment data for a credit card
        try {
            $creditCard = new CreditCardType();
            $creditCard->setCardNumber(cleanString($billingInformation->card_no));
            $creditCard->setCardCode($billingInformation->cvv);
            $creditCard->setExpirationDate($billingInformation->expiration);

            \Log::info('creditCard: ');
            \Log::info(json_encode($creditCard));

            $paymentOne = new PaymentType();
            $paymentOne->setCreditCard($creditCard);

            \Log::info('PaymentType: ');
            \Log::info(json_encode($paymentOne));

            $orderType = new OrderType();
            $orderType->setInvoiceNumber($invoice->uuid);
            $orderType->setDescription("An order From Homedeliverybr");

            \Log::info('OrderType: ');
            \Log::info(json_encode($orderType));

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

            \Log::info('CustomerAddressType: ');
            \Log::info(json_encode($customerAddress));

            // Set the customer's identifying information
            $customerData = new CustomerDataType();
            $customerData->setType($invoice->user->account_type);
            $customerData->setId($invoice->user->pobox_number);
            $customerData->setEmail($invoice->user->email);

            \Log::info('CustomerDataType: ');
            \Log::info(json_encode($customerData));

            // Transaction Request
            $transactionRequestType = new TransactionRequestType();
            $transactionRequestType->setTransactionType('authCaptureTransaction');

            if ($invoice->differnceAmount()) {
                
                $transactionRequestType->setAmount(
                    round($invoice->differnceAmount(), 2)
                );
                
            } else 
            {
                $transactionRequestType->setAmount(
                    round($invoice->total_amount, 2)
                );
            }
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setOrder($orderType);

            \Log::info('TransactionRequestType: ');
            \Log::info(json_encode($transactionRequestType));

            $request = new CreateTransactionRequest();
            $request->setMerchantAuthentication($this->merchantAuthentication);
            $request->setRefId($this->refId);

            $request->setTransactionRequest($transactionRequestType);
            $controller = new CreateTransactionController($request);

            \Log::info('Request: ');
            \Log::info(json_encode($request));
            
            if ( app()->environment('production') ){
                $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
                \Log::info('AuthorizeNetService: '.json_encode($response));

                \Log::info('Production Environment');
            }else{
                $response = $controller->executeWithApiResponse(ANetEnvironment::SANDBOX);
                \Log::info('AuthorizeNetService: '.json_encode($response));
                
                \Log::info('Sandbox Environment');
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
            
            if ($tResponse->getTransId() != null && $tResponse->getErrors() == null) {
                return (object)[
                    'success' => true,
                    'data' => $tResponse,
                    'message' => 'Transaction Successfully Made With Transaction ID: '.$tResponse->getTransId()
                ];
            }
            

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
