@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
@endsection
@section('page')

@if($order->has_second_label)
<div class="card pb-3">
    <div class="row mr-3">
        <div class="ml-auto mt-5">
            <button onclick="window.open('{{ route('order.us-label.download',[$order,'time'=>md5(microtime())]) }}','','top:0,left:0,width:600px;height:700px;')" class="btn btn-primary">Download</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary ml-2 pull-right">
                @lang('shipping-rates.Return to List')
            </a>
        </div>
    </div>
    <div class="container">
        <div class="label mt-2">
            <iframe src="https://docs.google.com/gview?url={{ route('order.us-label.download',$order) }}&embedded=true&time{{md5(microtime())}}" style="width:100%; height:700px;" frameborder="0">
                <iframe src="{{ route('order.us-label.download',$order) }}" style="width:100%; height:700px;" frameborder="0"></iframe>
            </iframe>
        </div>
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <h4 class="card-title" id="basic-layout-form"></h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary pull-right">
            @lang('shipping-rates.Return to List')
        </a>

        <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
        <div class="heading-elements">
            <ul class="list-inline mb-0">
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
            </ul>
        </div>
    </div>
    <hr>
    <div class="ml-3">
        <div class="row ml-3">
            <h2 class="mb-2">
                Order Details
            </h2>
        </div>
        <div class="row mt-3 ml-3">
            <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
            <div class="col-md-3">
                <h4>Order ID: {{ $order->warehouse_number }}</h4>
            </div>
            <div class="col-md-3">
                <h4>Tracking Code : {{ $order->corrios_tracking_code }}</h4>
            </div>
            <div class="col-md-3">
                <h4>Weight : {{ $order->getWeight('kg')  }} Kg</h4>
            </div>
            <div class="col-md-3">
                <h4>POBOX # : {{ $order->user->pobox_number }} </h4>
            </div>
        </div>
    </div>
    @if ($order->getWeight('kg') < 31) <form action="{{ route('admin.orders.usps-label.store', $order) }}" method="POST">
        @csrf
        <div class="ml-3 mt-3">
            <div class="row ml-3">
                <h2 class="mb-2">
                    Sender Address
                </h2>
            </div>
            <div class="container">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="first_name" value="{{ old('first_name', __default($order->sender_first_name, optional($order->user)->name)) }}" id="first_name" placeholder="Enter your First Name" required>
                        <div id="first_name_error">

                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name',__default($order->sender_last_name,optional($order->user)->last_name)) }}" id="last_name" placeholder="Enter your last Name" required>
                        <div id="last_name_error">

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="state">Select State <span class="text-danger">*</span></label>
                        <select name="sender_state" id="sender_state" class="form-control selectpicker" data-live-search="true" required>
                            <option value="" disabled>Select @lang('address.State')</option>
                            @foreach ($states as $state)
                            <option {{ old('sender_state') == $state->id ? 'selected' : '' }} value="{{ $state->code }}" data-state-code="{{$state->code}}">{{ $state->code }}</option>
                            @endforeach
                        </select>
                        <div id="state_error">

                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sender_address">Sender Address <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="sender_address" value="{{ old('sender_address', __default($order->sender_address ?? '', $order->sender_address ?? '' ))}}" id="sender_address" placeholder="Enter you street address">
                        <div id="address_error">

                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="sender_city">Sender City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="sender_city" value="{{ old('sender_city', __default($order->sender_city ?? '', $order->sender_city ?? '' )) }}" id="sender_city" placeholder="Enter your city">
                        <div id="city_error">

                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="sender_zipcode">Sender Zip Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="sender_zipcode" value="{{ old('sender_zipcode') }}" id="sender_zipcode" placeholder="Enter your zipcode">
                        <div id="zipcode_response">

                        </div>
                    </div>
                </div>
                <input type="hidden" name="total_price" value="{{ old('total_price') }}" id="total_price">
            </div>
        </div>
        <div class="ml-3 mt-3">
            <div class="row ml-3">
                <h2 class="mb-2">
                    Service
                </h2>
            </div>
            <div class="container pb-5">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="inputEmail4">Choose Service <span class="text-danger">*</span></label>
                        <select name="service" id="usps_shipping_service" class="form-control selectpicker dropup" data-dropup-auto="false" data-live-search="true" required>
                            <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                            @foreach ($shippingServices as $shippingService)
                            <option value="{{ $shippingService->service_sub_class }}" {{ old('service',$order->shipping_service_id) == $shippingService->service_sub_class ? 'selected' : '' }} data-service-code="{{$shippingService->service_sub_class}}">{{ "{$shippingService->name}"}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6 mt-4" id="calculated_rates">

                    </div>
                </div>
            </div>
        </div>
        <div class="container pb-3">
            <div class="row mr-3">
                <div class="ml-auto">
                    <button type="submit" id="submitBtn" class="btn btn-primary" disabled>Buy USPS Label</button>
                </div>
            </div>
        </div>
        </form>
        @else
        <div class="container">
            <div class="row mb-3 col-12 alert alert-danger">
                <h5 class="text-danger">USPS is not available for more than 30 Kg</h5>
            </div>
        </div>
        @endif
</div>
@endif

@endsection
@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });
</script>
@include('admin.orders.label.script')

@endsection