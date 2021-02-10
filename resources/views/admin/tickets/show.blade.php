@extends('layouts.master')
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 text-left">
                            @lang('tickets.Support Tickets') 
                            <i class="feather icon-chevron-right"></i> 
                            <u>#{{ $ticket->getHumanID() }}</u>
                            <div class="h6 mt-2">
                                @lang('tickets.note')
                            </div>
                        </h4>
                        <a href="{{ route('admin.tickets.index') }}" class="pull-right btn btn-primary"> 
                            @lang('tickets.Back To List')
                        </a>
                    </div>
                    <hr>
                    
                    <div class="card-content">
                        <div class="card-body">
                            @if( $errors->count() )
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <h4 class="mb-0 pull-left">{{ $ticket->subject }}</h4>
                                    <div class="h4 pull-right">
                                        <strong>@lang('tickets.Status')</strong> <span class="border-bottom"> @if($ticket->open == 1) <span class="badge badge-success">@lang('tickets.open')</span> @else <span class="badge badge-danger">@lang('tickets.close')</span> @endif </span>
                                    </div>
                                </div>

                            </div>
                            <hr>
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
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if( $ticket->isOpen() )

                                <form novalidate="" action="{{ route('admin.tickets.update',$ticket->id) }}" class="" method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row justify-content-center">
                                    <div class="form-group col-6">
                                        <div class="controls">
                                            <textarea class="form-control w-100" value="{{ old('detail') }}"
                                                      placeholder="" name="text"></textarea>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1 justify-content-center">
                                    <div class="col-6 d-flex">
                                        <button type="submit"
                                                class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('tickets.Reply')
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
