@extends('layouts.master')
@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">@lang('parcel.Create Parcel')</h4>
                    <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> @lang('parcel.Back to List')</a>
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
                        <form novalidate="" action="{{ route('admin.parcels.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @admin
                            <div class="row mt-1">
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                        <livewire:components.search-user />
                                        @error('pobox_number')
                                        <div class="help-block text-danger"> {{ $message }} </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endadmin
                            <div class="row mt-1">
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@lang('parcel.Sender Inside') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="merchant" value="{{ old('merchant') }}" placeholder="">
                                        @error('merchant')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@lang('parcel.Carrier Inside') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ old('carrier') }}" placeholder=""  name="carrier">
                                        @error('carrier')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@lang('parcel.Tracking Inside')<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="tracking_id" id="trackingInput" value="{{ old('tracking_id') }}" placeholder="" maxlength="22">
                                        @error('tracking_id')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                @can('addShipmentDetails', App\Models\Order::class)
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@lang('parcel.External Customer Reference')<span class="text-danger"></span></label>
                                        <input type="text" class="form-control" value="{{ old('customer_reference') }}" placeholder="" name="customer_reference" maxlength="22">
                                        @error('customer_reference')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                @endcan
                                <div class="col-12 col-sm-4">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="controls">
                                                    @admin
                                                    <label>@lang('parcel.Arrival Date')<span class="text-danger">*</span></label>
                                                    @endadmin
                                                    @user
                                                    <label>@lang('parcel.Order Date')<span class="text-danger">*</span></label>
                                                    @enduser
                                                    <input type="text" name="order_date" class="form-control order_date_picker datepicker" value="{{ old('order_date') }}" required="" placeholder="@user @lang('parcel.Order Date') @enduser @admin @lang('parcel.Arrival Date') @endadmin" />
                                                    @error('order_date')
                                                    <div class="help-block text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@lang('parcel.Invoice')</label>
                                        <input type="file" name="invoiceFile" {{ auth()->user()->isUser() ? 'required': ''  }} class="form-control" placeholder="@lang('parcel.Choose Invoice File')">
                                        @error('record')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            @can('addWarehouseNumber', App\Models\Order::class)
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="controls">
                                        <label>@lang('parcel.Warehouse Number') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" required name="whr_number" value="{{ old("whr_number") }}" placeholder="">
                                        @error('whr_number')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endcan
                            @can('addShipmentDetails', App\Models\Order::class)
                            <livewire:order.shipment-info />
                            <div class=" mt-4">


                                <!-- Modal -->
                                <div class="modal fade" id="uploadModal" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background: #B8C2CC;">
                                                <h5 class="modal-title" id="uploadModalLabel">Shipment Images</h5>
                                                <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <button id="webcamBtn" class="btn btn-secondary text-dark mx-2" type="button">Upload via Webcam</button>
                                                    <button id="fileBtn" class="btn btn-secondary text-dark mx-2" type="button">Upload from PC</button>
                                                </div>
                                                <div id="previewContainer" class="d-flex flex-wrap justify-content-center"></div>
                                                <div class="webcam-container text-center" style="display:none;">
                                                    <video id="webcam-video" width="400" height="400" autoplay></video>
                                                    <button id="takePhotoBtn" class="btn btn-secondary mt-2" type="button">Take Photo</button>
                                                </div>
                                                <input type="file" accept="image/*" name="images[]" id="fileInput" class="d-none" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Checkboxes -->
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button class="btn btn-primary" id="openUploadModalBtn" type="button">Upload Shipment Images</button>
                                    </div>
                                    <div>
                                        <div>
                                            <div class="form-check form-check-inline mx-4 text-center">
                                                <div class="vs-checkbox-con vs-checkbox-primary" title="Battery">
                                                    <input type="radio" name="order_contain_option" value="battery" id="battery"
                                                        {{ old('order_contain_option') == 'battery' ? 'checked' : '' }}>
                                                    <span class="vs-checkbox vs-checkbox-lg">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                </div> <label class="form-check-label font-medium-1 font-weight-bold mt-2 ml-2" for="battery">
                                                    <strong>
                                                        Contains UN3481
                                                    </strong>
                                                </label>

                                            </div>
                                            <br>
                                            <div class="form-check form-check-inline mx-4 text-center">
                                                <div class="vs-checkbox-con vs-checkbox-primary" title="Perfume">
                                                    <input type="radio" name="order_contain_option" value="perfume" id="perfume"
                                                        {{ old('order_contain_option') == 'perfume' ? 'checked' : '' }}>
                                                    <span class="vs-checkbox vs-checkbox-lg">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                                <label class="form-check-label font-medium-1   mt-2" for="perfume">
                                                    <strong> ID 8000 </strong>
                                                    (consumer commodities)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    @endcan

                    <div class="row mt-1 m-3">
                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                            <button type="reset" class="btn btn-lg btn-outline-danger waves-effect mr-lg-3 waves-light">@lang('parcel.Reset')</button>
                            <button type="submit" class="btn btn-lg btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 mx-2 waves-effect waves-light">
                                @lang('parcel.Save Parcel')
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
<script src="{{ asset('js/pages/webcam.js') }}"></script>
@endsection