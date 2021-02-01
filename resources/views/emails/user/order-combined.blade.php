@component('mail::message')
@lang('email.consolidation-ready.Hello')

@lang('email.consolidation-ready.Your consolidation request') {{ $order->warehouse_number }}

@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
