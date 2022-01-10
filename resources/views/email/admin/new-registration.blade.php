@component('mail::message')
Hello, <br>
You have received a new form submission for the form Main Form - PT. Here are the details: <br>
Obtenha seu endereço <br>
Receba seus produtos facilmente <br>
<br>
<strong>Nome:</strong>	{{ $user->name }} {{ $user->last_name }} <br>
<strong>Email:</strong>	{{ $user->email }} <br>
<strong>Referral:</strong>	{{ $user->come_from }} <br>
<strong>Telefone:</strong> {{ $user->phone }} <br>
<strong>Unique ID:</strong> {{ $user->pobox_number }} <br>
<strong>Date:</strong> {{ $user->created_at->format('Y-m-d') }} <br>
<strong>Time:</strong> {{ $user->created_at->format('g:i:s a') }}
@endcomponent
