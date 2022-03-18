@extends('layouts.master')

@section('page')
<div class="card min-vh-100">
    <div class="card-header">
        <h4 class="mb-0">Create Sale Order</h4>
        <a href="{{ route('admin.inventory.product.index') }}" class="pull-right btn btn-primary">Return to List</a>
    </div>
    <div class="card-content">
        <div class="card-body no-print" style="overflow-y: visible">
            <form class="form" action="{{ route('admin.inventory.product-order.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-body">
                    @foreach ($products as $product)
                        <div class="row col-12">
                            <div class="form-group col-6 col-sm-6 col-md-6">
                                <div class="controls">
                                    <label>Product Name<span class="text-danger">*</span></label>
                                    <input type="text" disabled class="form-control" name="name[]" value="{{ $product->name }} | {{ $product->sku }}" required>
                                    <input type="hidden" class="form-control" name="ids[]" value="{{ $product->id }}" required>
                                    
                                </div>
                            </div>
                            <div class="form-group col-6 col-sm-6 col-md-6">
                                <div class="controls">
                                    <label>Quantity<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="quantity[]" value="" required placeholder="Enter Product Quantity" required>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
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
