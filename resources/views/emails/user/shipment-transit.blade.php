@component('mail::message')
@lang('email.package-transit.Hello') {{ $order->user->name }},
 

@lang('email.package-transit.Your package created')


@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.package-transit.Regards')<br>
@lang('email.package-transit.HD-team')
@endcomponent
