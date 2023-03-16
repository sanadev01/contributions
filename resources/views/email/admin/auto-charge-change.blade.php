@component('mail::message')
Hello, <br>
User details<br>
IP address: {{ Request()->ip() }}<br>
<strong> UserName:</strong> {{ $user->full_name }} <strong>Pobox No.:</strong> {{ $user->pobox_number }} charge setting has been updated on 
<strong>Date:</strong> {{ date('Y-m-d') }} 
<strong>Time:</strong> {{ date('g:i:s a') }}<br>
@if($newData['status']=='Active')
<p style="text-align: justify;">
&nbsp; &nbsp; &nbsp; &nbsp;HERCO FREIGHT DBA Homedeliverybr
2200 NW 129th Ave, Miami, FL, 33182
+1(305)888-5191
Recurring Payment Authorization Form
Schedule your payment to be automatically deducted from your Visa, MasterCard,
American Express or
Discover Card that is on file in the HERCO FREIGHT DBA Home DeliveryBR website.<br>
Here’s How Recurring Payments Work:
You authorize regularly scheduled charges to your credit card on file, based on
metrics you set up. You
will be charged the amount indicated every time your balance hits the amount you
determine.
Each transaction may be viewed in your account, under ‘’activity’’. You may change
the metrics or
disable this function at your discretion.
</p>
<p style="text-align: justify;">
&nbsp; &nbsp; &nbsp; &nbsp; I <u> &nbsp; {{ $user->full_name }} &nbsp;</u> authorize HERCO FREIGHT DBA
Home DeliveryBR to charge
my credit card
that is on file account to the metrics I insert on my account.
</p>

@component('mail::table')
| | |
| ------------- |------------- | 
| Billing Address |   <u> &nbsp;   {{  $newData['card']}}   </u> |    
| Phone#  |   <u> &nbsp; {{ $user->phone }} &nbsp;</u>  |   
| Country, State, Zip|<u>  &nbsp;{{ optional($selectedCard)->country }},  {{ optional($selectedCard)->state }},  {{ optional($selectedCard)->zipcode }}  </u> | 
| Email# | <u> &nbsp; {{ auth()->user()->email }} &nbsp;</u> |
|Date# |<u> &nbsp; {{ date('Y-m-d') }} &nbsp;</u> |
@endcomponent
<p style="text-align: justify;">
&nbsp; &nbsp; &nbsp; &nbsp; I understand that this authorization will remain in effect until I cancel it in
writing or disable the function on my HERCO FREIGHT DBA Home DeliveryBR account.
</p>
<p style="text-align: justify;">
&nbsp; &nbsp; &nbsp; &nbsp; I understand that not having sufficient funds in my account may effect my ability to
generate labels and pay for taxes and duties.
For ACH debits to my checking/savings account, I understand that because these are
electronic transactions,these funds may be withdrawn from my account as soon as the above noted periodic
transaction dates. In the case of an ACH Transaction being rejected for Non
Sufficient Funds (NSF).<br>
</p>
<p style="text-align: justify;">
&nbsp; &nbsp; &nbsp; &nbsp; I understand that HERCO FREIGHT DBA Home DeliveryBR may at its discretion attempt to
process the charge again within
30 days, and agree to an additional charge for each attempt returned NSF which will
be initiated as a separate transaction from the authorized recurring payment.
I understand that I will need to give written authorization in case additional funds
may need to be debited from my card,and exceed my current balance and or auto debit.
I acknowledge that the origination of ACH transactions to my account must comply
with the provisions of U.S. law. <br>
</p>

<p style="text-align: justify;">
&nbsp; &nbsp; &nbsp; &nbsp;I certify that I am an authorized user of this credit card/bank account and will
not dispute these scheduled transactions with my bank or credit card company; so long as the transactions
correspond to the terms indicated in this authorization form.<br>
</p>
@endif
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
