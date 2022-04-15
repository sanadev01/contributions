@component('mail::message')
@component('mail::panel')
@lang('email.welcome.Dear') {{ $user->name }} {{ $user->last_name }}
@endcomponent



@lang('email.welcome.Thank you for')
@lang('email.welcome.You may begin shopping')
<br>

@lang('email.welcome.Your Home DeliveryBR suite') {{ $user->pobox_number }} <br>
@lang('email.welcome.Your Home DeliveryBR address') <br>
{{ $user->name }} {{ $user->last_name }} <br>
@lang('email.welcome.Address line 1:'){!! $user->pobox? $user->pobox->address : ' 2200 NW, 129th Ave – Suite # 100' !!} <br>
@lang('email.welcome.Address line 2:'){{ $user->address2 ? $user->address2 : ' Miami, FL, 33182' }} <br>
@lang('email.welcome.City:') {!! $user->pobox? $user->pobox->city : ' Miami' !!} <br>
@lang('email.welcome.State:') {!! $user->pobox? $user->pobox->state : ' Florida' !!} <br>
@lang('email.welcome.Zip code:'){!! $user->pobox? $user->pobox->zipcode : ' 33182' !!} <br>


@lang('email.welcome.You may use our') <br>
@lang('email.welcome.Your dashboard will')

@component('mail::button', ['url' => route('login') ])
DASHBOARD
@endcomponent


@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
