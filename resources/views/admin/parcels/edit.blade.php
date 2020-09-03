@extends('layouts.master')

@section('css')
    <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css') }}">
@endsection

@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('prealerts.edit-prealert')</h4>
                        @admin
                        <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> Back to List </a>
                        @endadmin

                        @user
                        <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> @lang('prealerts.back-to-list') </a>
                        @enduser
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
                            @admin
                                <form novalidate="" action="{{ route('admin.parcels.update',$prealert) }}" method="post" enctype="multipart/form-data">
                            @endadmin

                            @user
                            <form novalidate="" action="{{ route('admin.parcels.update',$prealert) }}" method="post" enctype="multipart/form-data">
                            @enduser
                                @csrf
                                @method('PUT')
                                <div class="row mt-1">
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('prealerts.merchant') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="merchant" value="{{ old('merchant',$prealert->merchant) }}" placeholder="">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('prealerts.carrier') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ old('carrier',$prealert->carrier) }}" placeholder=""  name="carrier">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('prealerts.tracking-id') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="carrier_tracking_id" value="{{ old('carrier_tracking_id',$prealert->carrier_tracking_id) }}" placeholder="">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12 col-sm-4">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <label>@lang('prealerts.order-date') <span class="text-danger">*</span></label>
                                                        <input type="text" name="order_date" class="form-control order_date_picker" value="{{ old('order_date',$prealert->order_date->format('Y-m-d')) }}" required="" placeholder="Order date">
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('prealerts.invoice')</label>
                                            @admin
                                            <input type="file" name="invoiceFile" class="form-control" placeholder="Choose Invoice File">
                                            @endadmin
                                            @user
                                            <input type="file" name="invoiceFile" class="form-control" placeholder="Choose Invoice File" required>
                                            @enduser
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
{{--                                    <div class="col-12 col-sm-4">--}}
{{--                                        <div class="form-group">--}}
{{--                                            <div class="controls">--}}
{{--                                                <label>Country <span class="text-danger">*</span></label>--}}
{{--                                                <select name="country_id" class="form-control" id="">--}}
{{--                                                    @foreach( countries() as $country )--}}
{{--                                                        <option value="{{ $country->id }}" {{ $country->id == $prealert->country_id?'selected':'' }}>{{ $country->name }}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}
{{--                                                <div class="help-block"></div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </div>
                                {{-- Items Portions starts here --}}
{{--                                <div id="pre-alert-items">--}}
{{--                                    <item-wrapper :items='{{ old("items",$prealert->items) }}'></item-wrapper>--}}
{{--                                </div>--}}
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('prealerts.save-prealert')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('prealerts.reset')</button>
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


@section('js')
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js') }}"></script>
    <script !src="">
        if ($(".order_date_picker").length > 0) {
            $('.order_date_picker').pickadate({
                format: 'yyyy-m-d',
                max: 0
            });
        }
    </script>
@endsection
