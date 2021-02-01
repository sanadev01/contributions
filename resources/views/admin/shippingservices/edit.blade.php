@extends('layouts.master')
@section('page')
    <section id="vue-handling-services">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('shippingservice.Edit Shipping Service')</h4>
                        <a href="{{ route('admin.shipping-services.index') }}" class="btn btn-primary">
                            @lang('shippingservice.Back to List')
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
                            <form action="{{ route('admin.shipping-services.update', $shippingService) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')


                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Name')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                    <input type="text" required class="form-control" value="{{$shippingService->name}}" name="name" value="{{ old('name') }}" placeholder="@lang('shippingservice.Name')">
                                        @error('name')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Max length allowed')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->max_length_allowed}}" name="max_length_allowed" placeholder="@lang('shippingservice.Max length allowed')" value="{{ old('max_length_allowed') }}">
                                        @error('max_length_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Max width allowed')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->max_width_allowed}}" name="max_width_allowed" placeholder="@lang('shippingservice.Max width allowed')" value="{{ old('max_width_allowed') }}">
                                        @error('max_width_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Min length allowed')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->min_length_allowed}}" name="min_length_allowed" placeholder="@lang('shippingservice.Min length allowed')" value="{{ old('min_length_allowed') }}">
                                        @error('min_length_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Min width allowed')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->min_width_allowed}}" name="min_width_allowed" placeholder="@lang('shippingservice.Min width allowed')" value="{{ old('min_width_allowed') }}">
                                        @error('min_width_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Max sum of all sides')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->max_sum_of_all_sides}}" name="max_sum_of_all_sides" placeholder="@lang('shippingservice.Max sum of all sides')" value="{{ old('max_sum_of_all_sides') }}">
                                        @error('max_sum_of_all_sides')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.max_weight')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" name="max_weight_allowed" placeholder="@lang('shippingservice.max_weight')" value="{{ old('max_weight_allowed',$shippingService->max_weight_allowed) }}">
                                        @error('max_weight_allowed')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Contains battery changes')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->contains_battery_charges}}" name="contains_battery_charges" placeholder="@lang('shippingservice.Contains battery changes')" value="{{ old('contains_battery_charges') }}">
                                        @error('contains_battery_charges')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Contains perfume charges')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->contains_perfume_charges}}" name="contains_perfume_charges" placeholder="@lang('shippingservice.Contains perfume charges')" value="{{ old('contains_perfume_charges') }}">
                                        @error('contains_perfume_charges')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Contains flammable liquid charges')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" required class="form-control" value="{{$shippingService->contains_flammable_liquid_charges}}" name="contains_flammable_liquid_charges" placeholder="@lang('shippingservice.Contains flammable liquid charges')" value="{{ old('contains_flammable_liquid_charges') }}">
                                        @error('contains_flammable_liquid_charges')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('shippingservice.Name')Active<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="active" required value="{{ old('active') }}" placeholder="active" >
                                            <option value="">@lang('shippingservice.Active')</option>
                                            <option @if($shippingService->active == 1) selected @endif value="1">Yes</option>
                                            <option @if($shippingService->active == 0) selected @endif value="0">No</option>
                                        </select>
                                        @error('active')
                                            <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('shippingservice.Save Changes')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('shippingservice.Reset')</button>
                                    </div>
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
