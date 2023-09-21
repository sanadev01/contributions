@component('mail::message')
@lang('email.new-ticket-comment-added.Hello')! {{ $ticketComment->ticket->user->name }}
<br>
@lang('email.new-ticket-comment-added.Add comment') {{ $ticketComment->ticket->getHumanID() }}
<br>

{!! $ticketComment->text !!}

@component('mail::button', ['url' => route('login')])
Dashboard
@endcomponent

@lang('email.contactUs') <br>
@lang('email.Home DeliveryBR Team')
@endcomponent
