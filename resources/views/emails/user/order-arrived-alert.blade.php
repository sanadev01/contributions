@component('mail::message')

@lang('email.package-ready.Hello') <strong> {{ $order->user->name }}</strong>,
<p style="text-align: justify">
    We are happy to inform you that your order  <strong> {{ $order->warehouse_number }} </strong> successfully arrived at our warehouse on <strong> {{ date_format(date_create($order->arrived_date),"Y-m-d") }} at {{ date_format(date_create($order->arrived_date),"H:i:s") }} </strong>   and is ready for further processing. We appreciate your patience and cooperation throughout the order fulfilment process.
</p> 
@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
