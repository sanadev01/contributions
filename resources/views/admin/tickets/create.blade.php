@extends('layouts.master')
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0 text-center">
                            @lang('tickets.note')
                        </h4>
                        <a href="{{ route('admin.tickets.index') }}" class="pull-right btn btn-primary"> @lang('tickets.Back To List') </a>
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
                                    <h4 class="mb-0">@lang('tickets.Create New Ticket') </h4>
                                </div>
                            </div>
                            <form novalidate="" action="{{ route('admin.tickets.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row mt-1 justify-content-center">
                                    <div class="form-group col-6 col-sm-6 col-md-6">
                                        <div class="controls">
                                            <label>@lang('tickets.Subject')  <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" placeholder="@lang('tickets.Subject')">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="form-group col-6">
                                        <div class="controls">
                                            <label>@lang('tickets.Details')</label>
                                            <textarea class="form-control w-100" placeholder=""  name="text">{{old('text')}}</textarea>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1 justify-content-center">
                                    <div class="col-6 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('tickets.Save')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('tickets.Reset')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
