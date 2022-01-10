@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">Add New Product</h4>
        <a href="{{ route('admin.inventory.product.index') }}" class="pull-right btn btn-primary">Return to List</a>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <form class="form" action="{{ route('admin.inventory.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-body">
                  
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
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="" placeholder="Enter Product Name">
                                @error('name')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Price <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="price" value="" placeholder="Enter Product Price">
                                @error('price')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>SKU<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku" value="" placeholder="Enter Product SKU">
                                @error('sku')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                         
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('orders.order-details.order-item.Harmonized Code')<span class="text-danger"></span></label>
                                <livewire:components.search-sh-code class="form-control" required name="sh_code"/>
                                @error("sh_code")
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Description<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="description" value="" placeholder="Enter Product description">
                                @error('description')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Quantity<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" value="" placeholder="Enter Product quantity">
                                @error('quantity')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('parcel.Merchant')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="merchant" value="{{ old('merchant') }}" placeholder="">
                                @error('merchant')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('parcel.Carrier') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ old('carrier') }}" placeholder=""  name="carrier">
                                @error('carrier')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('parcel.Tracking ID')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="tracking_id" value="{{ old('tracking_id') }}" placeholder="">
                                @error('tracking_id')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        
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
                                            <input type="text" name="order_date" class="form-control order_date_picker datepicker" value="{{ old('order_date') }}" required="" placeholder="@user @lang('parcel.Order Date') @enduser @admin @lang('parcel.Arrival Date') @endadmin"/>
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

                    {{-- @can('addWarehouseNumber', App\Models\Order::class) --}}
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
                    {{-- @endcan --}}

                    {{-- @can('addShipmentDetails', App\Models\Order::class) --}}
                        <livewire:order.shipment-info />
                    {{-- @endcan --}}
                    
                    <div class="row mt-1">
                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                            <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                @lang('profile.Save')
                            </button>
                            <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('profile.Reset')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
   
</div>
@endsection
