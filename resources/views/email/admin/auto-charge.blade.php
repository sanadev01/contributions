@component('mail::message')
Hello, HomeDeliveryBr <br>
Please charge to the customer {{ $user->pobox_number  }} of USD {{ $charge }}

@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
