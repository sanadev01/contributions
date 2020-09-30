@component('mail::message')
@lang('email.payment-done.Hello')

@lang('email.payment-done.Your payment was processed',['invoiceId'=>$invoice->uuid,'last-four-digit' => $invoice->last_four_digits])

@lang('email.payment-done.Within 48 business hours')

@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
