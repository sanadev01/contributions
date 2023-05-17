@extends('layouts.master')

@section('page')
    <div class="card min-vh-100">
        <div class="card-header d-flex justify-content-end">
        @section('title', __('Create Sale Order'))
        <a href="{{ route('admin.inventory.product.index') }}" class="pull-right btn btn-primary">Return to List</a>
    </div>
    <div class="card-content">
        <div class="card-body no-print paddinglr" style="overflow-y: visible">
            <form class="form" action="{{ route('admin.inventory.product-order.store') }}" method="POST"
                enctype="multipart/form-data">
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
                    @if ($isSingle)
                        <div class="row col-12">
                            <div class="form-group col-6 col-sm-6 col-md-6">
                                <div class="controls">
                                    <label>Product Name<span class="text-danger">*</span></label>
                                    <input type="text" disabled class="form-control" name="name[]"
                                        value="{{ $product->name }} | {{ $product->sku }}" required>
                                    <input type="hidden" class="form-control" name="ids[]"
                                        value="{{ $product->id }}" required>

                                </div>
                            </div>
                            <div class="form-group col-6 col-sm-6 col-md-6">
                                <div class="controls">
                                    <label>Quantity<span class="text-danger">*</span> <b>Quantity Available
                                            {{ $product->quantity }}</b></label>
                                    <input type="number" class="form-control"
                                        name="items[{{ 0 }}][quantity]" value="{{ old('quantity') }}"
                                        required placeholder="Enter Product Quantity" min="1"
                                        max="{{ $product->quantity }}" required>
                                    @error('items.0.quantity')
                                        <div class="help-block text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach ($products as $key => $product)
                            <div class="row col-12">
                                <div class="form-group col-6 col-sm-6 col-md-6">
                                    <div class="controls">
                                        <label>Product Name<span class="text-danger">*</span></label>
                                        <input type="text" disabled class="form-control" name="name[]"
                                            value="{{ $product->name }} | {{ $product->sku }}" required>
                                        <input type="hidden" class="form-control" name="ids[]"
                                            value="{{ $product->id }}" required>

                                    </div>
                                </div>
                                <div class="form-group col-6 col-sm-6 col-md-6">
                                    <div class="controls">
                                        <label>Quantity<span class="text-danger">*</span> <b>Quantity Available
                                                {{ $product->quantity }}</b></label>
                                        <input type="number" class="form-control"
                                            name="items[{{ $key }}][quantity]" value="" required
                                            placeholder="Enter Product Quantity" min="1"
                                            max="{{ $product->quantity }}" required>
                                        @error("items.{$key}.quantity")
                                            <div class="help-block text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="row mt-1">
                        <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                            <button type="submit"
                                class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                @lang('profile.Save')
                            </button>
                            <button type="reset"
                                class="btn btn-outline-warning waves-effect waves-light">@lang('profile.Reset')</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
