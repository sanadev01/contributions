@extends('layouts.master')

@section('page') 
    <div class="card">
        <div class="card-header">
            <h1 class="card-title" id="basic-layout-form">@lang('profitpackage.create-package')</h1>
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
            <div class="card-body">
                <form class="form" action="{{ route('admin.rates.profit-packages.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row justify-content-center mt-1">
                        <div class="col-md-10">
                            <label for="">@lang('profitpackage.package-name')</label>
                            <input type="text" class="form-control" name="package_name" value="{{ old('package_name') }}">
                            @error('package_name')
                                <div class="text-danger">
                                    {{ $message }}
                                </div> 
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-center mt-1">
                        <div class="col-md-10">
                            <label for="">@lang('profitpackage.package-type')</label>
                            <select class="form-control" name="type">
                                <option value="custom" {{ old('package_name') == '' ? 'custom': '' }}>Custom</option>
                                <option value="default" {{ old('package_name') == 'default' ? 'selected': '' }}>Default</option>
                            </select>
                            @error('package_name')
                                <div class="text-danger">
                                    {{ $message }}
                                </div> 
                            @enderror
                        </div>
                    </div>
                    <div class="row justify-content-center mt-1">
                        <div class="col-md-10">
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
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <livewire:user.profit.slabs>
                        </div>
                    </div>

                    <div class="form-actions pl-5 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="la la-check-square-o"></i> @lang('profitpackage.save')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
