@component('mail::message')
Hello, {{ $user->full_name }}<br>
The <strong>{{ Auth::user()->name }}</strong> has created a transaction on <strong>Date:</strong> {{ $deposit->updated_at->format('Y-m-d') }} at <strong>Time:</strong> {{ $deposit->updated_at->format('g:i:s a') }}. The transaction detail is given below.<br>

<strong>Login User: </strong>{{ Auth::user()->name }}<br>
<strong>Pobox No:</strong> {{ $user->pobox_number }}<br>

<strong>Transaction details</strong> <br>
@component('mail::table')
| Previous Balance   | Transaction Amount | Remaining Balance   	|
| :-------------: |:-------------:| :-------------:		|
|  {{ $deposit->balance - $deposit->amount }}   USD | {{ $deposit->amount }} USD |  {{ $deposit->balance }} USD |
@endcomponent

<strong>Auto charge Settings</strong> <br>
@component('mail::table')
| Charge Amount  | Charge limit | Billing information |
| :-------------: |:-------------:| :-------------:	|
|  {{ setting('charge_amount', null, $user->id) }} USD |  {{ setting('charge_limit', null, $user->id)  }} USD | {{  $cardNo }} |
@endcomponent

@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
