<div wire:poll.60s>
    <div class="row justify-content-center">
        <div class="col-md-6">
            @foreach($ticket->comments as $comment)
                <div class="card" style="background-color: {{ $comment->isSent() ? '#dcdcdc': '' }}">
                    <div class="card-header">
                        <div class="icon d-flex pull-left align-items-center">
                            <i class="feather icon-user rounded bg-dark text-white rounded-circle p-1 mr-2"></i>
                            <h4>{{ $comment->user->name }}</h4>
                        </div>
                        <div class="date">
                            <h5>{{ $comment->created_at->format('M d Y g:i a') }}</h5>
                        </div>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <p class="mb-3">
                                {!! $comment->text !!}
                            </p>
                            <hr>
                            <p>
                                @foreach ($comment->images as $image)
                                    @if( in_array($image->type, $extensions))
                                        <a target="_blank" href="{{ $image->getPath() }}" class="m-2">
                                            <img src="{{ $image->getPath() }}" alt="{{ $image->name }}" width="200" height="200">
                                        </a>
                                    @else
                                        <a target="_blank" href="{{ $image->getPath() }}" class="m-2"> {{ $image->name }} </a>
                                    @endif
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>                     
</div>
