@component('mail::message')
Hello, {{ $user }}<br>
The <strong>{{ $user }}</strong> has created the a transaction on <strong>Date:</strong> {{ $created->format('Y-m-d') }} at <strong>Time:</strong> {{ $created->format('g:i:s a') }}. The transaction detail is given below.<br>

<strong>Login User: </strong>{{ $user }}<br>
<strong>Order Username: </strong> {{ $order->user->name }}<br>
<strong>Pobox No:</strong> {{ $order->user->pobox_number }}<br>
<strong>Warehouse No:</strong> {{ $order->id }} <br>
<strong>Order Status (Previous): </strong> {{ $pre_status }}<br>
<strong>Order Status (Current): </strong> {{ $new_status }}<br>
<strong>Previous Balance:</strong> {{ $pre_balance }} USD<br>
<strong>Transaction Amount:</strong> {{ $amount }} USD<br>
<strong>Remaining Balance:</strong> {{ $rem_balance }} USD<br>
@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
