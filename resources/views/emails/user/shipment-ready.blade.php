@component('mail::message')
@lang('email.package-arrived.Hello') {{ $preAlert->user->name }},
 

@lang('email.package-arrived.Your package arrived')


@lang('email.package-arrived.Your warehouse number') {{ $preAlert->shipment->whr_number }}


@lang('email.package-arrived.Please log in')<br>
@lang('email.package-arrived.Hello')

@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
