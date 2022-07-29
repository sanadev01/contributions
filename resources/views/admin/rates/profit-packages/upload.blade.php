@extends('layouts.master')

@section('page')
    <div class="card">
        <div class="card-header d-flex justify-content-end">
        @section('title', __('profitpackage.upload-profit-package'))
        <a class="btn btn-primary" href="{{ route('admin.rates.profit-packages.index') }}">
            @lang('profitpackage.back to list')
        </a>

        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="card-content collapse show">
        <div class="col-12 mb-5">
            <div class="btn-group pull-right">
                <div class="dropdown">
                    <button type="button" class="btn btn-success pull-right mt-3 mr-1 dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Download Samples
                    </button>
                    <div class="dropdown-menu overlap-menu" aria-labelledby="dropdownMenuLink">
                        @isset($shipping_services)
                            @foreach ($shipping_services as $service)
                                <a class="dropdown-item"
                                    href="{{ route('admin.rates.rates.exports', ['package' => 10, 'service' => $service->id]) }}">
                                    <i class="fa fa-arrow-down"></i> {{ $service->name }} Download Sample
                                </a>
                            @endforeach
                        @endisset
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body paddinglr">
            <form class="form mt-4" action="{{ route('admin.rates.profit-packages-upload.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="row justify-content-center mt-1">
                    <div class="col-md-6">
                        <label for="">@lang('profitpackage.package-name')</label>
                        <input type="text" class="form-control" name="package_name"
                            value="{{ old('package_name') }}">
                        @error('package_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="row justify-content-center mt-1">
                    <div class="col-md-6">
                        <label for="">@lang('profitpackage.package-type')</label>
                        <select class="form-control" name="type">
                            <option value="custom" {{ old('type') == '' ? 'custom' : '' }}>Custom</option>
                            <option value="default" {{ old('type') == 'default' ? 'selected' : '' }}>Default</option>
                        </select>
                        @error('type')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="row justify-content-center mt-1">
                    <div class="col-md-6">
                        <label for="">@lang('profitpackage.Shipping Service')</label>
                        <select name="shipping_service_id" required class="form-control">
                            <option value="" selected>@lang('profitpackage.Select Service')</option>
                            @isset($shipping_services)
                                @foreach ($shipping_services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-center mt-4">
                    <div class="input-group col-md-6 p-0">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Upload</span>
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file" value="{{ old('file') }}">
                            <label class="custom-file-label" for="inputGroupFile01">@lang('profitpackage.package-slab')<span
                                    class="text-danger">*</span></label>
                        </div>
                    </div>
                </div>


                <div class="form-actions pl-5 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-check-square-o"></i> @lang('profitpackage.import')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
