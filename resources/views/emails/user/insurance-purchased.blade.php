@component('mail::message')
@lang('email.package-transit.Hello') {{ $order->user->name }},
 

@lang('An Insurance has been purchased. Details are as under.')

@lang('PO Box Number:') {{ $order->pobox_number }}
@lang('Warehouse Number:') {{ $order->warehouse_number }}
@lang('Tracking Code:') {{ $order->corrios_tracking_code }}


@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.package-transit.Regards')<br>
@lang('email.package-transit.HD-team')
@endcomponent
