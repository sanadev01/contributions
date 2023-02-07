@extends('layouts.master')
@section('page')
<section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">@lang('warehouse.containers.Edit Container')</h4>
                    <a href="{{ route('warehouse.usps_containers.index') }}" class="pull-right btn btn-primary">@lang('warehouse.containers.List Containers')</a>
                </div>
                <hr>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('warehouse.mile-express-containers.update', $container) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Seal No')<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="seal_no" required value="{{ old('seal_no', $container->seal_no) }}">
                                    @error('seal_no')
                                    <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Container Type')<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control" name="unit_type" value="{{ old('unit_type') }}" required>
                                        <option value="">@lang('warehouse.containers.Container Type')</option>
                                        <option value="1" {{ old('unit_type', $container->unit_type) == '1' ? 'selected' : '' }}>BAG</option>
                                        <option value="2" {{ old('unit_type', $container->unit_type) == '2' ? 'selected' : '' }}>BOX</option>
                                    </select>
                                    @error('unit_type')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Destination Airport')<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control" name="destination_operator" disabled>
                                        <option value="">@lang('warehouse.containers.Destination Airport')</option>
                                        <option value="SAOD" {{ old('destination_operator_name', $container->destination_operator_name) == 'SAOD' ? 'selected' : '' }}>GRU</option>
                                        <option value="CRBA" {{ old('destination_operator_name', $container->destination_operator_name) == 'CRBA' ? 'selecte' : '' }}>CWB</option>
                                    </select>
                                    @error('destination_operator_name')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                                <input type="hidden" class="form-control" name="destination_operator_name" value="{{ $container->destination_operator_name }}">
                            </div>
                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Distribution Service Class')<span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control" name="services_subclass_code" required>
                                        <option value="">@lang('warehouse.containers.Distribution Service Class')</option>
                                        <option value="ML-EX" {{ old('services_subclass_code', $container->services_subclass_code) == 'ML-EX' ? 'selected': '' }}>Mile Express</option>
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
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection()