@extends('layouts.master')

@section('page')
    <div class="card">
        <div class="card-header d-flex justify-content-end">
        @section('title', __('profitpackage.edit-profit-package'))
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
        <div class="card-body paddinglr">
            <form class="form" action="{{ route('admin.rates.profit-packages.update', $profitPackage) }}"
                method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row justify-content-center mt-1">
                    <div class="col-md-10">
                        <label for="">@lang('profitpackage.package-name')</label>
                        <input type="text" class="form-control" name="package_name"
                            value="{{ old('package_name', $profitPackage->name) }}">
                        @error('package_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="row justify-content-center mt-1">
                    <div class="col-md-10">
                        <label for="">@lang('profitpackage.package-name')</label>
                        <select class="form-control" name="type">
                            <option value="custom"
                                {{ old('package_name', $profitPackage->type) == '' ? 'custom' : '' }}>Custom</option>
                            <option value="default"
                                {{ old('package_name', $profitPackage->type) == 'default' ? 'selected' : '' }}>Default
                            </option>
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
                                    <option value="{{ $service->id }}"
                                        {{ $profitPackage->shipping_service_id == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <livewire:user.profit.slabs :profit_id='$profitPackage->id' />
                    </div>
                </div>
                <div class="container form-actions pl-5 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="la la-check-square-o"></i> @lang('profitpackage.update')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        $(".rate").keyup(function() {
            var profit = $(this).val();
            var key = $(this).data('key');
            var shipping = $('#shipping_' + key).val();
            var result = (parseFloat(shipping) * (parseFloat(profit) / 100)) + parseFloat(shipping);
            $('#selling_' + key).val(result.toFixed(2));
        });
        $(".selling").keyup(function() {
            var selling = $(this).val();
            var key = $(this).data('key');
            var shipping = $('#shipping_' + key).val();
            var result = ((parseFloat(selling) - parseFloat(shipping)) * 100) / parseFloat(shipping);
            $('#profit_' + key).val(result.toFixed(2));

        });
    });
</script>
@endsection
