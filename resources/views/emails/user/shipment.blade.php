@component('mail::message')

@lang('email.package-ready.Hello') <strong> {{ $user->name }}</strong>,
<p style="text-align: justify">
    We are happy to inform you that your {{ count($orders)==1?'order':'orders'}} successfully arrived at our warehouse at <strong>{{ date_format(date_create($orders->first()->arrived_date),"Y-m-d") }}</strong> and is ready for further processing. We appreciate your patience and cooperation throughout the order fulfilment process.Check Attachment for more details.
</p> 
@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
