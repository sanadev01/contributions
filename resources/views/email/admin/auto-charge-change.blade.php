@component('mail::message')
Hello, <br>
User details<br>
IP address: {{ Request()->ip() }}<br>
<strong> UserName:</strong> {{ $user->full_name }} <strong>Pobox No.:</strong> {{ $user->pobox_number }} charge setting has been updated on 
<strong>Date:</strong> {{ date('Y-m-d') }} 
<strong>Time:</strong> {{ date('g:i:s a') }}<br>

@component('mail::table')
| Setting Name  | Old Values    | New Values	|
| ------------- |:-------------:| --------:		| 
| Status |  {{ $oldData['status']}} | 	 {{ $newData['status']}} |    
| Charge amount |  {{ $oldData['amount']}} | 	 {{ $newData['amount']}} |      	
| Charge limit  |  {{ $oldData['limit']}} | {{ $newData['limit']}} |      
| Billing Info  |  {{ $oldData['card']}} |  {{ $newData['card']}} |
@endcomponent

@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
