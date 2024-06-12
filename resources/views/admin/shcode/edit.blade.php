@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Update ShCode</h4>
                        <a href="{{ route('admin.roles.index') }}" class="pull-right btn btn-primary">@lang('role.Back to List') </a>
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
                            <form action="{{ route('admin.shcode.update',$shcode->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">ShCode<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="code" value="{{ old('code', $shcode->code) }}" placeholder="Enter ShCOde">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">English<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="en" value="{{ old('en', optional(explode('-------',$shcode->description))[0]) }}" placeholder="Enter English language Details">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Portuguese<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="pt" value="{{ old('pt', optional(explode('-------',$shcode->description))[1]) }}" placeholder="Enter Portuguese language Details">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Spanish<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="sp" value="{{ old('sp', optional(explode('-------',$shcode->description))[2]) }}" placeholder="Enter Spanish language Details">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Type<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="type">
                                            <option value="Postal (Correios)">Postal (Correios)</option>
                                            <option value="Courier" @if($shcode->type == 'Courier') selected @endif>Courier</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('role.Update')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('role.Reset')</button>
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
