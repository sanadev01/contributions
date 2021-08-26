@extends('admin.orders.layouts.wizard')
@section('wizard-css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
@section('wizard-form')
@if ($error)
    <div class="alert alert-danger" role="alert">
        {{$error}}
    </div>
@endif
<form action="{{ route('admin.orders.order-details.store',$order) }}" method="POST" class="wizard">
    @csrf
    <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
    <div class="content clearfix">
        <!-- Step 1 -->
        <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">@lang('orders.order-details.Step 1')</h6>
        <fieldset role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current p-4" aria-hidden="false">
            <div class="row">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.order-details.Customer Reference') <span class="text-danger"></span></label>
                        <input name="customer_reference" class="form-control" value="{{ $order->customer_reference }}" placeholder="@lang('orders.order-details.Customer Reference')"/>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.order-details.WHR')# <span class="text-danger"></span></label>
                        <input class="form-control" readonly value="{{ $order->warehouse_number }}" placeholder="@lang('orders.order-details.Warehouse Number')"/>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label class="h4">Freight <span class="text-danger"></span></label>
                        <input class="form-control" name="user_declared_freight" id="user_declared_freight" value="{{ old('user_declared_freight', $order->user_declared_freight) }}" placeholder="Freight"/>
                        {{-- <input class="form-control" name="user_declared_freight" id="user_declared_freight" value="{{ old('user_declared_freight',__default($order->user_declared_freight,$order->gross_total)) }}" placeholder="Freight"/> --}}
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <h4 class="mt-2">@lang('orders.order-details.Service')</h4>
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.order-details.Select Shipping Service')<span class="text-danger"></span></label>
                        @if ($order->recipient->country_id != 250)
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="shipping_service_id" id="shipping_service_id" required placeholder="Select Shipping Service">
                            <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                            @foreach ($shippingServices as $shippingService)
                                <option value="{{ $shippingService->id }}" {{ old('shipping_service_id',$order->shipping_service_id) == $shippingService->id ? 'selected' : '' }} data-cost="{{$shippingService->getRateFor($order)}}" data-services-cost="{{ $order->services()->sum('price') }}">{{ "{$shippingService->name} - $". $shippingService->getRateFor($order) }}</option>
                            @endforeach
                        </select>
                        @else
                        {{-- for usps --}}
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="shipping_service_id" id="usps_shipping_service" required placeholder="Select Shipping Service">
                            <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                            @foreach ($shippingServices as $shippingService)
                                <option value="{{ $shippingService->id }}" {{ old('shipping_service_id',$order->shipping_service_id) == $shippingService->id ? 'selected' : '' }} data-service-code="{{$shippingService->service_sub_class}}">{{ "{$shippingService->name}"}}</option>
                            @endforeach
                        </select>
                        @endif
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.order-details.Tax Modality') <span class="text-danger"></span></label>
                        <select class="form-control selectpicker show-tick" name="tax_modality" id="tax_modality" readonly required placeholder="@lang('orders.order-details.Tax Modality')">
                            <option value="ddu" selected >DDU</option>
                            {{-- <option value="ddp" {{ 'ddp' == $order->tax_modality ? 'selected' : '' }}>DDP</option> --}}
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <hr>
            <livewire:order.order-details.order-items :order-id="$order->id"/>
            <hr>
            <div class="row mt-1">
                <div class="form-group col-12">
                    @lang('orders.order-details.declaration')
                </div>
            </div>
        </fieldset>
    </div>
    <div class="actions clearfix">
        <ul role="menu" aria-label="Pagination">
            <li class="disabled" aria-disabled="true">
                @if ($order->user->hasRole('retailer'))
                    <a href="{{ route('admin.orders.services.index',$order) }}" role="menuitem">@lang('orders.order-details.Previous')</a>
                @else
                    <a href="{{ route('admin.orders.recipient.index',$order) }}" role="menuitem">@lang('orders.order-details.Previous')</a>
                @endif    
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button class="btn btn-primary">@lang('orders.order-details.Place Order')</button>
            </li>
        </ul>
    </div>
</form>
@endsection

@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>

<script>
    $('#shipping_service_id').on('change',function(){
        $('#user_declared_freight').val(
            parseFloat($('option:selected', this).attr("data-cost"))
        );
    })

    $('#usps_shipping_service').ready(function() {
        
        getUspsRates();
        
    })

    $('#usps_shipping_service').on('change',function(){
        getUspsRates();
    })
   
    function change(id){
        var id = "dangrous_"+id;  
        value = $('#'+id).val();
        if(value == 'contains_battery'){
            $(".dangrous").children("option[value^='contains_perfume']").hide()
        }
        if(value == 'contains_perfume'){
            $(".dangrous").children("option[value^='contains_battery']").hide()
        }
        if(value == 0){
            $(".dangrous").children("option[value^='contains_battery']").show();
            $(".dangrous").children("option[value^='contains_perfume']").show();
        }
    }

    function getUspsRates(){
        const service = $('#usps_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        
        $('#loading').fadeIn();
        $.get('{{ route("api.usps_rates") }}',{
                service: service,
                order_id: order_id,
            }).then(function(response){
                if(response.success == true){
                    $('#user_declared_freight').val(response.total_amount);
                    $('#user_declared_freight').prop('readonly', true);
                }
                $('#loading').fadeOut();

            }).catch(function(error){
                console.log(error);
                $('#loading').fadeOut();
        })
        
    }
</script>
@endsection