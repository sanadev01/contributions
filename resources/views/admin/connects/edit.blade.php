@extends('layouts.master')
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-end">
                    @section('title', __('connect.Integrate Shopify Store'))
                    <a href="{{ route('admin.connect.index') }}" class="pull-right btn btn-primary">@lang('role.Back to List')</a>
                </div>
                <div class="card-content">
                    <div class="card-body paddinglr">
                        <form action="{{ route('admin.connect.update', $connect) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">@lang('connect.Name')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="connect_name"
                                        value="{{ old('connect_name', $connect->store_name) }}"
                                        placeholder="@lang('connect.Name')">
                                    @error('connect_name')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">@lang('connect.Name')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="connect_name"
                                        value="{{ old('connect_name', $connect->store_name) }}"
                                        placeholder="@lang('connect.Name')">
                                    @error('connect_name')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">@lang('connect.Default Shipping Service')<span
                                        class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select class="form-control" name="default_shipping_service">
                                        <option value="">Select Default Shipping Service</option>
                                        @foreach (shippingServices() as $shippingService)
                                            <option value="{{ $shippingService->id }}"
                                                {{ old('default_shipping_service', $connect->default_shipping_service) == $shippingService->id ? 'selected' : '' }}>
                                                {{ $shippingService->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('default_shipping_service')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mt-1">
                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit"
                                        class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                        @lang('role.Save')
                                    </button>
                                    <button type="reset"
                                        class="btn btn-outline-warning waves-effect waves-light">@lang('role.Reset')</button>
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
