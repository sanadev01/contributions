@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
@section('page') 
    <div class="card">
        <div class="card-header">
            <h4 class="card-title" id="basic-layout-form">@lang('shipping-rates.BPS Rates')</h4>
            <a href="{{ route('admin.rates.shipping-rates.index') }}" class="btn btn-primary pull-right">
                @lang('shipping-rates.Return to List')
            </a>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <form class="form" action="{{ route('admin.rates.shipping-rates.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-body">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <h4 class="form-section">@lang('shipping-rates.Import BPS Leve Rates Excel')</h4>
                            </div>
                        </div>
                        <div class="row justify-content-center">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>@lang('shipping-rates.Shipping Service') <span class="text-danger">*</span></label>
                                        <select name="shipping_service_id" required class="form-control">
                                            @isset($shipping_services)
                                                @foreach ($shipping_services as $service)
                                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                                @endforeach
                                            @endisset
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="row justify-content-center">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="controls">
                                        <label>@lang('shipping-rates.Country') <span class="text-danger">*</span></label>
                                        <select name="country_id" required class="form-control selectpicker show-tick" data-live-search="true" id="country">
                                                @foreach (countries() as $country)
                                                    <option {{ old('country_id') == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                                @endforeach
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="row justify-content-center">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="projectinput1">@lang('shipping-rates.Select Excel File to Upload')</label>
                                    <input type="file" class="form-control" name="csv_file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
                                    @error('csv_file')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="alert alert-warning">
                                    <ol>
                                        <li>@lang('shipping-rates.* Upload only Excel files')</li>
                                        <li>@lang('shipping-rates.* Files larger than 15Mb are not allowed')</li>
                                        <li>@lang('shipping-rates.* Download and fill in the data in the sample file below to avoid errors')</li>
                                        <li class="mt-2">@lang('shipping-rates.* Download the sample for bps rates')
                                             <div class="btn-group">
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        @lang('shipping-rates.Download')
                                                    </button>
                                                    <div class="dropdown-menu overlap-menu" aria-labelledby="dropdownMenuLink">
                                                        @isset($shipping_services)
                                                            @foreach ($shipping_services as $service)
                                                                <a href="{{ route('admin.rates.shipping-rates.show', $service->id) }}" target="_blank" class="dropdown-item">
                                                                    <i class="feather icon-download"></i>{{ $service->name }} @lang('shipping-rates.Download')
                                                                </a>
                                                            @endforeach
                                                        @endisset
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions pl-5">
                        <a href="{{ route('admin.rates.shipping-rates.index') }}" class="btn btn-warning mr-1 ml-3">
                            <i class="ft-x"></i> @lang('shipping-rates.Cancel')
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="la la-check-square-o"></i> @lang('shipping-rates.Import')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@endsection