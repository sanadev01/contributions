@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('warehouse.containers.Create Container')</h4>
                        <a href="{{ route('warehouse.gss_containers.index') }}" class="pull-right btn btn-primary">@lang('warehouse.containers.List Containers')</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('warehouse.gss_containers.store') }}" method="post" enctype="multipart/form-data">
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
                                            <option value="2" {{ old('unit_type') == '2' ? 'selected' : '' }}>BOX</option>
                                        </select>
                                        @error('unit_type')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <!-- <div class="controls row mb-1 align-items-center my-2">
                                    <label class="col-md-3 text-md-right">@lang('warehouse.containers.Destination Airport')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="destination_operator_name">
                                            <option value="">@lang('warehouse.containers.Destination Airport')</option>
                                            <option value="CWB" {{ old('destination_operator_name') == 'CWB' ? 'selected' : '' }}>CWB</option>
                                            <option value="SAO" {{ old('destination_operator_name') == 'SAO' ? 'selected' : '' }}>SAO</option>
                                            <option value="RIO" {{ old('destination_operator_name') == 'RIO' ? 'selected' : '' }}>RIO</option>
                                        </select>
                                        @error('destination_operator_name')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div> -->
                                <div class="controls row mb-1 align-items-center my-2">
                                    <label class="col-md-3 text-md-right">@lang('warehouse.containers.Distribution Service Class')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="services_subclass_code">
                                            <option value="">@lang('warehouse.containers.Distribution Service Class')</option>
                                            <!-- <option value="{{App\Models\ShippingService::GSS_PMI}}" {{ old('services_subclass_code') == App\Models\ShippingService::GSS_PMI ? 'selected': '' }}>Priority Mail International</option> -->
                                            <!-- <option value="{{App\Models\ShippingService::GSS_FCM}}" {{ old('services_subclass_code') == App\Models\ShippingService::GSS_FCM ? 'selected': '' }}>First Class Package International</option> -->
                                            <option value="{{App\Models\ShippingService::GSS_EPMEI}}" {{ old('services_subclass_code') == App\Models\ShippingService::GSS_EPMEI ? 'selected': '' }}>Priority Mail Express International (Pre-Sort)</option>
                                            <option value="{{App\Models\ShippingService::GSS_EPMI}}" {{ old('services_subclass_code') == App\Models\ShippingService::GSS_EPMI ? 'selected': '' }}>Priority Mail International (Pre-Sort)</option>
                                            <!-- <option value="{{App\Models\ShippingService::GSS_EMS}}" {{ old('services_subclass_code') == App\Models\ShippingService::GSS_EMS ? 'selected': '' }}>Priority Mail Express International (Nationwide)</option> -->
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
