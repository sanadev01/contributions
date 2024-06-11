@component('mail::message')
@lang('email.tracking-available.Hello') {{ $tracking->order->user->name }}

@lang('email.tracking-available.Your tracking number') <strong>WHR# {{ $tracking->order->shipment->whr_number }}</strong>

@lang('email.tracking-available.Please allow 3-4')

@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
