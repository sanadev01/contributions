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
| Battery		| {{ $oldData['battery'] }}   	| {{$request->battery  ? 'Active':'Inactive'}}			|      	
| Perfume		| {{ $oldData['perfume'] }} | {{$request->perfume  ? 'Active':'Inactive'}}			|     
| Insurance		| {{ $oldData['insurance'] }} | {{$request->insurance  ? 'Active':'Inactive'}}			|      	  
| USPS			| {{ $oldData['usps'] }} | {{$request->usps  ? 'Active':'Inactive'}}			|     
| UPS			| {{ $oldData['ups'] }} | {{$request->ups  ? 'Active':'Inactive'}}			|
| Sinerlog		| {{ $oldData['sinerlog'] }} | {{$request->sinerlog  ? 'Active':'Inactive'}}			|     
| Fedex			| {{ $oldData['fedex'] }} | {{$request->fedex  ? 'Active':'Inactive'}}			|
| tax			| {{ $oldData['tax'] }} | {{$request->tax  ? 'Active':'Inactive'}}			|     
| Vol Discount	| {{ $oldData['volumetric_discount'] }} | {{$request->volumetric_discount ? 'Active':'Inactive'}}			|
| UPS Profit	| {{ $oldData['ups_profit'] }} | {{$request->ups_profit}}			|     
| USPS Profit	| {{ $oldData['usps_profit'] }} | {{$request->usps_profit}}			|     
| Discount %	| {{ $oldData['discount_percentage'] }} | {{$request->discount_percentage}}			|
| Fedex Profit	| {{ $oldData['fedex_profit'] }} | {{$request->fedex_profit}}			|     
| Weight		| {{ $oldData['weight'] }} | {{$request->weight}}			|
| Length		| {{ $oldData['length'] }} | {{$request->length}}			|     
| Width			| {{ $oldData['width'] }} | {{$request->width}}			|
| Height		| {{ $oldData['height'] }} | {{$request->height}}			|
@endcomponent

@component('mail::button', ['url' => route('login') ])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
