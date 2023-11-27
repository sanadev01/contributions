@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('warehouse.containers.Create Container')</h4>
                        <a href="{{ route('warehouse.postnl_containers.index') }}" class="pull-right btn btn-primary">@lang('warehouse.containers.List Containers')</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('warehouse.postnl_containers.store') }}" method="post" enctype="multipart/form-data">
                                @csrf

                                <div class="controls row mb-1 align-items-center my-2">
                                    <label class="col-md-3 text-md-right">@lang('warehouse.containers.Seal No')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="seal_no" required value="{{ old('seal_no') }}">
                                        @error('seal_no')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center my-2">
                                    <label class="col-md-3 text-md-right">@lang('warehouse.containers.Container Type')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="unit_type" value="{{ old('unit_type') }}">
                                            <option value="">@lang('warehouse.containers.Container Type')</option>
                                            <option value="1" {{ old('unit_type') == '1' ? 'selected' : '' }}>BAG</option>
                                        </select>
                                        @error('unit_type')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center my-2">
                                    <label class="col-md-3 text-md-right">@lang('warehouse.containers.Origin Country')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" data-live-search="true" name="origin_country">
                                            <option value="">@lang('warehouse.containers.Origin Country')</option>
                                            @foreach (countries() as $country)
                                                <option value="{{ $country->code }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('origin_country')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center my-2">
                                    <label class="col-md-3 text-md-right">@lang('warehouse.containers.Destination Country')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="destination_country">
                                            <option value="">@lang('warehouse.containers.Destination Country')</option>
                                            @foreach (countries() as $country)
                                                <option value="{{ $country->code }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('destination_country')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center my-2">
                                    <label class="col-md-3 text-md-right">@lang('warehouse.containers.Distribution Service Class')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="services_subclass_code">
                                            <option value="">@lang('warehouse.containers.Distribution Service Class')</option>
                                            <option value="PostNL" {{ old('services_subclass_code') == 'PostNL' ? 'selected': '' }}>PostNL</option>
                                        </select>
                                        @error('services_subclass_code')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <hr class="mx-5 mt-5">
                                <div class="row mt-1">
                                    <div class="col-md-9 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light btn-lg">
                                            @lang('warehouse.containers.Save')
                                        </button>
                                        {{-- <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('role.Reset')</button> --}}
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
