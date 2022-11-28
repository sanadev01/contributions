@component('mail::message')
Hello, <br>
Login User details<br>
IP address: {{ $request->ip() }}<br>
<strong>Admin UserName:</strong> {{ auth()->user()->name }} {{ auth()->user()->last_name }} <strong>Pobox No.:</strong> {{ auth()->user()->pobox_number }} setting has been updated on 
<strong>Date:</strong> {{ $user->updated_at->format('Y-m-d') }} 
<strong>Time:</strong> {{ $user->updated_at->format('g:i:s a') }}<br>
User Setting's Details<br>
<strong>Username:</strong> {{ $user->name }} {{ $user->last_name }} <strong>Pobox No.:</strong> {{ $user->pobox_number }}<br>
Which has following Diffrence <br>

@component('mail::table')
| Setting Name  | Old Values    | New Values	|
| ------------- |:-------------:| --------:		|
@if (!$isAdmin)
| Battery		| {{ $userData['battery'] }}   	| {{$request->battery  ? 'Active':'Inactive'}}			|      	
| Perfume		| {{ $userData['perfume'] }} | {{$request->perfume  ? 'Active':'Inactive'}}			|     
| Insurance		| {{ $userData['insurance'] }} | {{$request->insurance  ? 'Active':'Inactive'}}			|      	  
| Sinerlog		| {{ $userData['sinerlog'] }} | {{$request->sinerlog  ? 'Active':'Inactive'}}           |     
| tax			| {{ $userData['tax'] }} | {{$request->tax  ? 'Active':'Inactive'}}			|     
| Vol Discount	| {{ $userData['volumetric_discount'] }} | {{$request->volumetric_discount ? 'Active':'Inactive'}}			|
| Discount %	| {{ $userData['discount_percentage'] }} | {{$request->discount_percentage}}			|
| Weight		| {{ $userData['weight'] }} | {{$request->weight}}			|
| Length		| {{ $userData['length'] }} | {{$request->length}}			|     
| Width			| {{ $userData['width'] }} | {{$request->width}}			|
| Height		| {{ $userData['height'] }} | {{$request->height}}			|
@else
| TYPE  | {{ $userData['TYPE'] }} | {{$request->TYPE}}			|
| VALUE | {{ $userData['VALUE'] }} | {{$request->VALUE}}			|
| AUTHORIZE_ID  | {{ substr($userData['AUTHORIZE_ID'],0,3) }} **** {{ substr($userData['AUTHORIZE_ID'],-3) }}| {{substr($request->AUTHORIZE_ID,0,3)}} **** {{substr($request->AUTHORIZE_ID,-3)}}  |
| AUTHORIZE_KEY | {{ substr($userData['AUTHORIZE_KEY'],0,3) }} **** {{  substr($userData['AUTHORIZE_KEY'],-3) }}| {{substr($request->AUTHORIZE_KEY,0,3)}} **** {{substr($request->AUTHORIZE_KEY,-3)}}			|
| API Active | {{ $userData['correios_setting'] }}| {{ ($request->correios_setting == 'anjun_api') ? 'Anjun API' : 'Correios API' }}			|
@endif
| USPS			| {{ $userData['usps'] }} | {{$request->usps  ? 'Active':'Inactive'}}			|     
| UPS			| {{ $userData['ups'] }} | {{$request->ups  ? 'Active':'Inactive'}}			|
| Fedex			| {{ $userData['fedex'] }} | {{$request->fedex  ? 'Active':'Inactive'}}			|
| USPS Profit	| {{ $userData['usps_profit'] }} | {{$request->usps_profit}}			|     
| UPS Profit	| {{ $userData['ups_profit'] }} | {{$request->ups_profit}}			|     
| Fedex Profit	| {{ $userData['fedex_profit'] }} | {{$request->fedex_profit}}			|     
@endcomponent

@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
