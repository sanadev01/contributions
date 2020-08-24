@foreach( $messages as $key => $message)
    <div class="alert {{ $key }}">
        <h4>{{ $name($key)  }} !</h4>
        <p>{!! $message !!}</p>
    </div>
@endforeach
