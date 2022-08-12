@component('mail::message')
@lang('email.Hello')

<p class="text-muted">This</p>
<br>

@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
