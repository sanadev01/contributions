@component('mail::message')
Hello,
<p class="text-justify">{{$message}}</p>
<br>
<p>{{$codes}}</p>
<br><br>
Name: {{$name}}<br>
POBOX#: {{$poBox}}
@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent

