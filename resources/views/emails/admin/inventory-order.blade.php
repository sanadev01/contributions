@component('mail::message')
Hello Admin,

{{ $order->user->pobox_number}} has just placed new order form Inventory.

@component('mail::button', ['url' => route('admin.orders.index')])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
