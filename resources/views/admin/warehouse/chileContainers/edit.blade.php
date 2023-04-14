@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                    @section('title', __('warehouse.containers.Edit Container'))
                    <a href="{{ route('warehouse.chile_containers.index') }}"
                        class="pull-right btn btn-primary">@lang('warehouse.containers.List Containers')</a>
                </div>
                <hr>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('warehouse.chile_containers.update', $container) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input hidden type="text" name="id" value="{{ $container->id }}">
                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Seal No')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="seal_no" required
                                        value="{{ old('seal_no', $container->seal_no) }}">
                                    @error('seal_no')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Container Type')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control" name="unit_type">
                                        <option value="">@lang('warehouse.containers.Container Type')</option>
                                        <option value="1"
                                            {{ old('unit_type', $container->unit_type) == '1' ? 'selected' : '' }}>BAG
                                        </option>
                                        <option value="2"
                                            {{ old('unit_type', $container->unit_type) == '2' ? 'selected' : '' }}>BOX
                                        </option>
                                    </select>
                                    @error('unit_type')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Origin Airport')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    @livewire('chile-container.search-airport', ['origin_operator_name' => $container->origin_operator_name])
                                    {{-- <select class="form-control" name="origin_operator_name">
                                            <option value="">@lang('warehouse.containers.Origin Airport')</option>
                                            <option value="MIA" {{ old('origin_operator_name', $container->origin_operator_name) == 'MIA' ? 'selected' : '' }}>MIA</option>
                                            <option value="HKG" {{ old('origin_operator_name', $container->origin_operator_name) == 'HKG' ? 'selected' : '' }}>HKG</option>
                                            <option value="JFK" {{ old('origin_operator_name', $container->origin_operator_name) == 'JFK' ? 'selected' : '' }}>JFK</option>
                                            <option value="ORD" {{ old('origin_operator_name', $container->origin_operator_name) == 'ORD' ? 'selected' : '' }}>ORD</option>
                                        </select> --}}
                                    @error('origin_operator_name')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Sorting')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control" name="destination_operator_name">
                                        <option value="">@lang('warehouse.containers.Sorting')</option>
                                        <option value="MR"
                                            {{ old('destination_operator_name', $container->destination_operator_name) == 'MR' ? 'selected' : '' }}>
                                            MR (Santiago)</option>
                                        <option value="RM"
                                            {{ old('destination_operator_name', $container->destination_operator_name) == 'RM' ? 'selected' : '' }}>
                                            RM (Other Region)</option>
                                    </select>
                                    @error('destination_operator_name')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center my-2">
                                <label class="col-md-3 text-md-right">@lang('warehouse.containers.Distribution Service Class')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control" name="services_subclass_code" disabled>
                                        <option value="">@lang('warehouse.containers.Distribution Service Class')</option>
                                        <option value="SRM"
                                            {{ old('services_subclass_code', $container->services_subclass_code) == 'SRM' ? 'selected' : '' }}>
                                            SRM</option>
                                        <option value="SRP"
                                            {{ old('services_subclass_code', $container->services_subclass_code) == 'SRP' ? 'selected' : '' }}>
                                            SRP</option>
                                    </select>
                                    @error('services_subclass_code')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                    @enderror
                                </div>
                            </div>
                            <hr class="mx-5 mt-5">
                            <div class="row mt-1">
                                <div class="col-md-9 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit"
                                        class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light btn-lg">
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
