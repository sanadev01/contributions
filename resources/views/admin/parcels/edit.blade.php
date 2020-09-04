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
                        <h4 class="mb-0">@lang('prealerts.create-prealert')</h4>
                        <a href="{{ route('admin.parcels.index') }}" class="pull-right btn btn-primary"> Back to List </a>
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
                                            <label>User POBOX Number <span class="text-danger">*</span></label>
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
                                            <label>@lang('prealerts.merchant') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="merchant" value="{{ old('merchant',$parcel->merchant) }}" placeholder="">
                                            @error('merchant')
                                                <div class="help-block text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('prealerts.carrier') <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ old('carrier',$parcel->carrier) }}" placeholder=""  name="carrier">
                                            @error('carrier')
                                                <div class="help-block text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group col-12 col-sm-6 col-md-4">
                                        <div class="controls">
                                            <label>@lang('prealerts.tracking-id')<span class="text-danger">*</span></label>
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
                                            <label>@lang('prealerts.customer_reference') <span class="text-danger"></span></label>
                                            <input type="text" class="form-control" value="{{ old('external_refrence',$parcel->external_refrence) }}" placeholder=""  name="external_refrence">
                                            @error('external_refrence')
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
                                                        <label>Arrival date <span class="text-danger">*</span></label>
                                                        @endadmin
                                                        @user
                                                        <label>@lang('prealerts.order-date') <span class="text-danger">*</span></label>
                                                        @enduser
                                                        <input type="text" name="order_date" class="form-control order_date_picker" value="{{ old('order_date',$parcel->order_date) }}" required="" 
                                                        placeholder="@user Order date @enduser @admin Arrival Date @endadmin"
                                                        >
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
                                            <label>@lang('prealerts.invoice')</label>
                                            <input type="file" name="invoiceFile" {{ auth()->user()->isUser() ? 'required': ''  }} class="form-control" placeholder="Choose Invoice File">
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
                                                <label>Warehouse Number <span class="text-danger">*</span></label>
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
                                    <h4 class="mt-2">Parcel Images</h4>
                                    <div class="row mt-1">
                                        <div class="col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>Images <span class="text-danger">*</span></label>
                                                <input type="file" accept="image/*" multiple name="images[]">
                                                {{-- @error('record')
                                                    <div class="help-block text-danger">{{ $message }}</div>
                                                @enderror --}}
                                            </div>

                                            <div class="mt-2">
                                                @foreach ($parcel->images as $image)
                                                    {{ $loop->index+1 }}. <a target="_blank" href="{{ $image->getPath() }}" class="m-2"> {{ $image->name }} </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endcan


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
