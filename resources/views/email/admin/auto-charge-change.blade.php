@component('mail::message')
Hello, <br>
User details<br>
IP address: {{ Request()->ip() }}<br>
<strong> UserName:</strong> {{ $user->name }} {{ $user->last_name }} <strong>Pobox No.:</strong> {{ $user->pobox_number }} charge setting has been updated on 
<strong>Date:</strong> {{ date('Y-m-d') }} 
<strong>Time:</strong> {{ date('g:i:s a') }}<br>

@component('mail::table')
| Setting Name  | Old Values    | New Values	|
| ------------- |:-------------:| --------:		|
@if(old('charge') ?? setting('charge', null, auth()->id()))
| Charge amount |  Inactive | 	 {{setting('charge_amount', null, auth()->id()) }} |      	
| Charge limit  |  Inactive | {{setting('charge_limit', null, auth()->id())}} |      
| Billing Info  |  Inactive |  {{ $cardNo}}|      
@else
| Charge amount |{{setting('charge_amount', null, auth()->id()) }} | Inactive |      	
| Charge limit  |{{setting('charge_limit', null, auth()->id())}}|  Inactive |     
| Billing Info  |{{$cardNo }}| Inactive |

@endif
@endcomponent

@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
