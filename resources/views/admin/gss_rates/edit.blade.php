@extends('layouts.master') 
@section('page')
    <section id="vue-handling-services">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('gssRate.Edit Service')</h4>
                        <a href="{{ route('admin.gss-rates.index') }}" class="btn btn-primary">
                            @lang('handlingservice.Back to List')
                        </a>
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
                            <form action="{{ route('admin.gss-rates.update',$gssRate) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') 
                                <div class="controls row mb-1 align-items-center mt-3">
                                    <label class="col-md-3 text-md-right">Select User<span class="text-danger">*</span></label>
                                    <label></label>
                                    <div class="col-md-6">
                                        <livewire:components.search-user selectedId="{{$gssRate->user_id}}" /> 
                                    </div>
                                </div> 

                                <div class="controls row mb-1 align-items-center mt-3">
                                    <label class="col-md-3 text-md-right">Shipping Service<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="shipping_service_id" required value="{{ old('shipping_service_id',$gssRate->shipping_service_id) }}" placeholder="shipping_service_id" id="shipping_service_id">
                                            <option value="">Select</option>
                                            @foreach ($shippingServices as $shippingService)
                                                <option value="{{$shippingService->id}}" {{ old('shipping_service_id',$gssRate->shipping_service_id) == $shippingService->id ? 'selected' : '' }}>{{$shippingService->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('shipping_service_id')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center mt-3">
                                    <label class="col-md-3 text-md-right">Country<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="country_id" required value="{{ old('country_id') }}" placeholder="country" id="country_id">
                                            <option value="">Select</option>
                                            @foreach ($countries as $key => $country)
                                                <option value="{{$country->id}}" {{ old('country_id',$gssRate->country_id) == $country->id ? 'selected' : '' }}>{{$country->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center mt-3">
                                    <label class="col-md-3 text-md-right">@lang('gssRate.Api Discount')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number"  min="1" class="form-control" name="api_discount" value="{{ old('api_discount',$gssRate->api_discount) }}" placeholder="@lang('gssRate.Api Discount')">
                                        @error('api_discount')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center mt-3">
                                    <label class="col-md-3 text-md-right">@lang('gssRate.User Discount')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" min="1"  class="form-control" name="user_discount" value="{{ old('user_discount',$gssRate->user_discount) }}" placeholder="@lang('gssRate.User Discount')">
                                        @error('user_discount')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div> 
                                
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('gssRate.Save Changes')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('gssRate.Reset')</button>
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
