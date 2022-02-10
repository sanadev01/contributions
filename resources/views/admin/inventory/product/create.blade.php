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
                                <label>Harmonized Code<span class="text-danger">*</span></label>
                                <livewire:components.search-sh-code class="form-control" name="sh_code" required />
                                @error('name')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Name<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Enter Product Name">
                                @error('name')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Order#<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="order" value="{{ old('order') }}" placeholder="Enter Product Order Number">
                                @error('order')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Price Per Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="price" value="{{ old('price') }}" placeholder="Enter Product Price">
                                @error('price')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Category <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="category" value="{{ old('category') }}" placeholder="Enter Product category">
                                @error('category')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>SKU<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku" value="{{ old('sku') }}" placeholder="Enter Product SKU">
                                @error('sku')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Description<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="description" value="{{ old('description') }}" placeholder="Enter Product description">
                                @error('description')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>Quantity<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" value="{{ old('quantity') }}" placeholder="Enter Product quantity">
                                @error('quantity')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Brand')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="brand" value="{{ old('brand') }}" placeholder="">
                                @error('brand')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Manufacturer') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" value="{{ old('manufacturer') }}" placeholder=""  name="manufacturer">
                                @error('manufacturer')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('BarCode')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="barcode" value="{{ old('barcode') }}" placeholder="">
                                @error('barcode')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('item#')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="item" value="{{ old('item') }}" placeholder="">
                                @error('item')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('lot#')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="lot" value="{{ old('lot') }}" placeholder="">
                                @error('lot')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Unit')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="unit" value="{{ old('unit') }}" placeholder="">
                                @error('unit')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Case')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="case" value="{{ old('case') }}" placeholder="">
                                @error('case')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Inventory Value')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="inventory_value" value="{{ old('inventory_value') }}" placeholder="">
                                @error('inventory_value')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Minimum Quantity')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="min_quantity" value="{{ old('min_quantity') }}" placeholder="">
                                @error('min_quantity')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Maximum Quantity')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="max_quantity" value="{{ old('max_quantity') }}" placeholder="">
                                @error('max_quantity')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Items Discontinued')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="discontinued" value="{{ old('discontinued') }}" placeholder="">
                                @error('discontinued')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Store Days')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="store_day" value="{{ old('store_day') }}" placeholder="">
                                @error('store_day')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-12 col-sm-6 col-md-4">
                            <div class="controls">
                                <label>@lang('Warehouse Location')<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="location" value="{{ old('location') }}" placeholder="">
                                @error('location')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                   
                    </div>
                    {{-- <livewire:order.shipment-info /> --}}
                    
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
