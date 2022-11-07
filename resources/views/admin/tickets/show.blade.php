@extends('layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endsection
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
                            @if ($errors->count())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
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
                                        <strong>@lang('tickets.Status')</strong> <span class="border-bottom">
                                            @if ($ticket->open == 1)
                                                <span class="badge badge-success">@lang('tickets.open')</span>
                                            @else
                                                <span class="badge badge-danger">@lang('tickets.close')</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <livewire:support-ticket.show-ticket :ticket="$ticket" />
                            @if ($ticket->isOpen())
                                <form novalidate="" action="{{ route('admin.tickets.update', $ticket->id) }}" class=""
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="form-group col-6 offset-3">
                                            <div class="controls">
                                                <textarea id="summernote" class="form-control w-100" value="{{ old('detail') }}" placeholder="" name="text"></textarea>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        @admin
                                            <livewire:support-ticket.note :ticket="$ticket" />
                                        @endadmin

                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-5 offset-3">
                                            <section class="section-preview">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroupFileAddon01">Attche
                                                            File</span>
                                                    </div>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input"
                                                            id="inputGroupFile02" name="file"
                                                            aria-describedby="inputGroupFileAddon01">
                                                        <label class="custom-file-label" for="inputGroupFile02">Choose
                                                            file</label>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="col-3">
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
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                height: 200,
            });
        });
    </script>
@endsection
