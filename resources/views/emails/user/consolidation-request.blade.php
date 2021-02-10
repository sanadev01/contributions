@component('mail::message')
@lang('email.consolidation-request.Hello')

@lang('email.consolidation-request.Your consolidation')

@lang('email.consolidation-request.A new warehouse number')

@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
