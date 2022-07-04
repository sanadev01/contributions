@component('mail::message')
@lang('email.package-ready.Hello') {{ $order->user->name }},
 

@lang('email.package-ready.Your package arrived')


@lang('email.package-ready.Additional service')



@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.package-ready.contact-us')<br>
@lang('email.package-ready.Regards')<br>
@lang('email.package-ready.HD-team') <br>
@endcomponent
