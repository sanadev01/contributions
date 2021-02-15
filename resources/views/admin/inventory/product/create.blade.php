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
                  

                    <div class="controls row mb-1 align-items-center">
                        <label class="col-md-3 text-md-right">Name</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="name" value="" placeholder="Enter Product Name">
                            @error('name')
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="controls row mb-1 align-items-center">
                        <label class="col-md-3 text-md-right">Price</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="price" value="" placeholder="Enter Product Price">
                            @error('price')
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="controls row mb-1 align-items-center">
                        <label class="col-md-3 text-md-right">SKU</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="sku" value="" placeholder="Enter Product SKU">
                            @error('sku')
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    {{-- <div class="controls row mb-1 align-items-center">
                        <label class="col-md-3 text-md-right">Active</label>
                        <div class="col-md-6">
                            <select name="status" id="" class="form-control" >
                                <option value="">Active Status</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            @error('status')
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div> --}}
                    <div class="controls row mb-1 align-items-center">
                        <label class="col-md-3 text-md-right">Description</label>
                        <div class="col-md-6">
                            <textarea type="text" class="form-control" name="description" rows="8" placeholder="Enter Product Description"></textarea>
                            @error('description')
                                <div class="help-block text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
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
