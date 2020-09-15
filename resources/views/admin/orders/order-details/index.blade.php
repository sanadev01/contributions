@extends('admin.orders.layouts.wizard')
@section('wizard-css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
@section('wizard-form')
<form action="{{ route('admin.orders.order-details.store',$order) }}" method="POST" class="wizard">
    @csrf
    <div class="content clearfix">
        <!-- Step 1 -->
        <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">Step 1</h6>
        <fieldset role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current p-4" aria-hidden="false">
            <div class="row">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>Customer Reference <span class="text-danger"></span></label>
                        <input name="customer_reference" class="form-control" value="{{ $order->customer_reference }}" placeholder="Customer Reference"/>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>WHR# <span class="text-danger"></span></label>
                        <input name="customer_reference" class="form-control" readonly value="{{ $order->warehouse_number }}" placeholder="Customer Reference"/>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <h4 class="mt-2">Service</h4>
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>Select Shipping Service <span class="text-danger"></span></label>
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="address_id" id="address_id" required placeholder="Select Shipping Service">
                            <option value="">Select Shipping Service</option>
                            @foreach ($shippingServices as $shippingService)
                                <option value="{{ $shippingService->id }}" {{ $shippingService->id == $order->shipping_service_id ? 'selected' : '' }}>{{ "{$shippingService->name} - $". $shippingService->getRateFor($order) }}</option>
                            @endforeach
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>Tax Modality <span class="text-danger"></span></label>
                        <select class="form-control selectpicker show-tick" name="tax_modality" id="tax_modality" required placeholder="Tax Modality">
                            <option value="ddu" {{ 'ddu' == $order->tax_modality ? 'selected' : '' }}>DDU</option>
                            <option value="ddp" {{ 'ddp' == $order->tax_modality ? 'selected' : '' }}>DDP</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <hr>
            <livewire:order.order-details.order-items :order-id="$order->id"/>
        </fieldset>
    </div>
    <div class="actions clearfix">
        <ul role="menu" aria-label="Pagination">
            <li class="disabled" aria-disabled="true">
                <a href="{{ route('admin.orders.services.index',$order) }}" role="menuitem">Previous</a>
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button class="btn btn-primary">Next</button>
            </li>
        </ul>
    </div>
</form>
@endsection

@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@endsection