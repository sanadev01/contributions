@component('mail::message')
@lang('Hello Team'),
 

@lang('An Insurance has been purchased. Details are as under.')
<br>
@lang('PO Box Number: '){{ $order->user->pobox_number }} <br>
@lang('Warehouse Number: '){{ $order->warehouse_number }}


@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('Regards')<br>
@lang('Home DeliveryBR Team')
@endcomponent
