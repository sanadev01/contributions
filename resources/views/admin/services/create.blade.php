@extends('layouts.master') 
@section('page')
    <section id="vue-handling-services">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('handlingservice.Create Service')</h4>
                        <a href="{{ route('admin.handling-services.index') }}" class="btn btn-primary">
                            @lang('handlingservice.Back to List')
                        </a>
                    </div>
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
                            <form action="{{ route('admin.handling-services.store') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('handlingservice.Name')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="@lang('handlingservice.Name of Service')">
                                        @error('name')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('handlingservice.Cost (USD)')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" step="0.001" class="form-control" name="cost" placeholder="@lang('handlingservice.Cost of Service')" value="{{ old('cost') }}">
                                        @error('cost')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('handlingservice.Price (USD)')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" step="0.001" class="form-control" name="price" placeholder="@lang('handlingservice.Price of Service')" value="{{ old('price') }}">
                                        @error('price')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('handlingservice.Save Changes')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('handingservice.Reset')</button>
                                    </div>
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
