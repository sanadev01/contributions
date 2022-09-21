@component('mail::message')
Hello, {{ $user }}<br>
The <strong>{{ $user }}</strong> has created a transaction on <strong>Date:</strong> {{ $deposit->updated_at->format('Y-m-d') }} at <strong>Time:</strong> {{ $deposit->updated_at->format('g:i:s a') }}. The transaction detail is given below.<br>

<strong>Login User: </strong>{{ $user }}<br>
@if(!empty($preStatus))
<strong>Order Username: </strong> {{ $order->user->name }}<br>
<strong>Pobox No:</strong> {{ $order->user->pobox_number }}<br>
<strong>Warehouse No:</strong> {{ $order->id }} <br>
<strong>Order Status (Previous): </strong> {{ $preStatus }}<br>
<strong>Order Status (Current): </strong> {{ $newStatus }}<br>
@endif
<strong>Previous Balance:</strong> {{ $deposit->balance - $deposit->amount }} USD<br>
<strong>@if(empty($order))Transaction @else Recharge @endif Amount:</strong> {{ $deposit->amount }} USD<br>
<strong>Remaining Balance:</strong> {{ $deposit->balance }} USD<br>
@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
