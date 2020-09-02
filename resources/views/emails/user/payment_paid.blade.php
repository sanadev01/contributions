@component('mail::message')
@lang('email.payment-done.Hello')

@lang('email.payment-done.Your payment was processed',['whr'=>$order->shipment->whr_number,'last-four-digit' => $order->last_four_digit])

@lang('email.payment-done.Within 48 business hours')

@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
