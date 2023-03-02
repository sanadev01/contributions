@component('mail::message')
Hello, {{ $user }}<br>
The <strong>{{ $user }}</strong> has perfomed an activity on <strong>Date:</strong> {{ $order->updated_at->format('Y-m-d') }} at <strong>Time:</strong> {{ $order->updated_at->format('g:i:s a') }}. The activity detail is given below.<br>

<strong>Login User: </strong>{{ $user }}<br>
<strong>Pobox No:</strong> {{ $order->user->pobox_number }}<br>
<strong>For Username: </strong> {{ $order->user->name }}<br>
@if(!empty($preStatus))
<strong>Warehouse No:</strong> {{ $order->id }} <br>
<strong>Order Status (Previous): </strong> {{ $preStatus }}<br>
<strong>Order Status (Current): </strong> {{ $newStatus }}<br>
@endif
@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
