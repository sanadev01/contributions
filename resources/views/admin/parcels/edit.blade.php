@extends('layouts.master')
@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">@lang('parcel.Edit Parcel')</h4>
                    <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> @lang('parcel.Back to List') </a>
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
                        <form novalidate="" action="{{ route('admin.parcels.update',$parcel) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @admin
                            <div class="row mt-1">
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@lang('parcel.User POBOX Number') <span class="text-danger">*</span></label>
                                        <livewire:components.search-user :selected_id="$parcel->user_id" />
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
                                        <label>@lang('parcel.Sender Inside')<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="merchant" value="{{ old('merchant',$parcel->merchant) }}" placeholder="">
                                        @error('merchant')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@if($parcel->products->isEmpty())@lang('parcel.Carrier Inside') @else @lang('parcel.Fulfillment Order Number') @endif<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" value="{{ old('carrier',$parcel->carrier) }}" placeholder="" name="carrier">
                                        @error('carrier')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group col-12 col-sm-6 col-md-4">
                                    <div class="controls">
                                        <label>@if($parcel->products->isEmpty()) @lang('parcel.Tracking Inside') @else @lang('parcel.Fulfillment Order Description') @endif<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="tracking_id" value="{{ old('tracking_id',$parcel->tracking_id) }}" placeholder="">
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
                                        <label>@if($parcel->products->isEmpty()) @lang('parcel.External Customer Reference') @else @lang('parcel.SKU Code') @endif<span class="text-danger"></span></label>
                                        <input type="text" class="form-control" value="{{ old('customer_reference',$parcel->customer_reference) }}" placeholder="" name="customer_reference">
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
                                                    <label>@if($parcel->products->isEmpty()) @lang('parcel.Arrival Date') @else @lang('parcel.Fulfillment Order Date') @endif<span class="text-danger">*</span></label>
                                                    @endadmin
                                                    @user
                                                    <label>@if($parcel->products->isEmpty()) @lang('parcel.Order Date') @else @lang('parcel.Fulfillment Order Date') @endif<span class="text-danger">*</span></label>
                                                    @enduser
                                                    <input type="text" name="order_date" class="form-control order_date_picker datepicker" value="{{ old('order_date',$parcel->order_date) }}" required=""
                                                        placeholder="@user @lang('parcel.Order Date') @enduser @admin @lang('parcel.Arrival Date') @endadmin">
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
                                        @if ( $parcel->purchaseInvoice )
                                        <div class="mt-2">
                                            <a target="_blank" href="{{ $parcel->purchaseInvoice->getPath() }}" class="m-2"> {{ $parcel->purchaseInvoice->name }} </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @can('addWarehouseNumber', App\Models\Order::class)
                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="controls">
                                        <label>@lang('parcel.Warehouse Number') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" required name="whr_number" value="{{ old("whr_number",$parcel->warehouse_number) }}" placeholder="">
                                        @error('whr_number')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            @endcan
                            @can('addShipmentDetails', App\Models\Order::class)
                            <livewire:order.shipment-info :order="$parcel" />
                            <h4 class="mt-2">@lang('parcel.Shipment Images') </h4>

                            <!-- Button to trigger the modal -->
                            <button class="btn btn-primary" id="openUploadModalBtn" type="button">Upload Images</button>
                            <!-- Modal -->
                            <div class="modal fade" id="uploadModal" aria-labelledby="uploadModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg m-100">
                                    <div class="modal-content w-100">
                                        <div class="modal-header w-100" style="background: #B8C2CC;">
                                            <h5 class="modal-title" id="uploadModalLabel">Shipment Images</h5>
                                            <button type="button" id="closeModal" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <button id="webcamBtn" class="btn btn-secondary mb-3 text-dark" type="button">Upload via Webcam</button>
                                            <button id="fileBtn" class="btn btn-secondary mb-3 text-dark" type="button">Upload from PC</button>

                                            <div id="previewContainer" class="d-flex flex-wrap">
                                            </div>
                                            <div class="webcam-container" style="display:none;">
                                                <video id="webcam-video" width="400" height="400" autoplay></video>
                                                <button id="takePhotoBtn" class="btn btn-secondary mt-1" type="button">Take Photo</button>
                                            </div>
                                            <input type="file" accept="image/*" name="images[]" id="fileInput" class="d-none" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endcan


                            <div class="row mt-1">
                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                    <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                        @lang('parcel.Save Parcel')
                                    </button>
                                    <button type="reset" class="btn btn-outline-danger waves-effect waves-light">@lang('parcel.Reset')</button>
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
<script>
    var images = @json($parcel->images);
    const canvas = document.createElement('canvas');
    images.forEach(image => {
        addOldImageToPreview('/storage/documents/' + image.path, canvas, 'image', 170, 170);
    });
</script>
@endsection