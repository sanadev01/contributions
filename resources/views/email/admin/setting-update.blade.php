@component('mail::message')
Hello, <br>
Login User details<br>
IP address: {{ $request->ip() }}<br>
Admin UserName: {{ auth()->user()->name }} {{ auth()->user()->last_name }} Pobox No.: {{ auth()->user()->pobox_number }} setting has been updated <br>
User Setting's Details<br>
Username:{{ $user->name }} {{ $user->last_name }} Pobox No.: {{ $user->pobox_number }}<br>
Which has following Diffrence <br>
<br>
<strong>Setting Name</strong>   ||    <strong>Old Values</strong>   ||    <strong>New Values</strong> <br>
	{{ $user->email }}   ||   {{ $user->email }}    ||    {{ $user->email }}<br>

<br>
<strong>Date:</strong> {{ $user->updated_at->format('Y-m-d') }} <br>
<strong>Time:</strong> {{ $user->updated_at->format('g:i:s a') }}
@endcomponent
