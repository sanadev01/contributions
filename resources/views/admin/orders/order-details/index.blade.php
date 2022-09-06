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
<div class="alert alert-danger" role="alert" id="ups_response" style="display: none;"></div>
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
                        <input name="customer_reference" class="form-control" {{($order->recipient->country_id == $countryConstants['Chile']) ? 'required' : ''}} value="{{ $order->customer_reference }}" placeholder="@lang('orders.order-details.Customer Reference')"/>
                        <p class="text-danger">{{ $errors->first('customer_reference') }}</p>
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
                        <input class="form-control" name="user_declared_freight" id="user_declared_freight" value="{{ old('user_declared_freight', $order->user_declared_freight) }}" placeholder="Freight" @if(optional($order)->sender_country_id == $countryConstants['US'] || optional($order->recipient)->country_id == $countryConstants['US']) readonly @endif/>
                        {{-- <input class="form-control" name="user_declared_freight" id="user_declared_freight" value="{{ old('user_declared_freight',__default($order->user_declared_freight,$order->gross_total)) }}" placeholder="Freight"/> --}}
                        <div class="help-block"></div>
                        <span class="text-danger">@error('user_declared_freight') {{ $message }} @enderror</span>
                    </div>
                </div>
            </div>
            <h4 class="mt-2">@lang('orders.order-details.Service')</h4>
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.order-details.Select Shipping Service')<span class="text-danger"></span></label>
                        @if ($order->recipient->country_id != $countryConstants['US'])
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="shipping_service_id" id="shipping_service_id" required placeholder="Select Shipping Service">
                            <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                            @foreach ($shippingServices as $shippingService)
                                <option value="{{ $shippingService->id }}" {{ old('shipping_service_id',$order->shipping_service_id) == $shippingService->id ? 'selected' : '' }} data-cost="{{$shippingService->getRateFor($order)}}" data-services-cost="{{ $order->services()->sum('price') }}" data-service-code="{{$shippingService->service_sub_class}}" data-service-code="{{$shippingService->service_sub_class}}">@if($shippingService->getRateFor($order)){{ "{$shippingService->name} - $". $shippingService->getRateFor($order) }}@else{{ $shippingService->name }}@endif</option>
                            @endforeach
                        </select>
                        @else
                        {{-- for usps,ups and fedex --}}
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="shipping_service_id" id="us_shipping_service" required placeholder="Select Shipping Service">
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
                <a href="{{ route('admin.orders.recipient.index',$order) }}" role="menuitem">@lang('orders.order-details.Previous')</a>   
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
    const shippingServiceCodes = @json($shippingServiceCodes)

    $("#rateBtn").hide();
    $('#shipping_service_id').on('change',function(){
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        
        if (service != shippingServiceCodes.COLOMBIA_Standard) {
            $('#user_declared_freight').val(
                parseFloat($('option:selected', this).attr("data-cost"))
            );
        }
        if(service == shippingServiceCodes.USPS_PRIORITY_INTERNATIONAL || service == shippingServiceCodes.USPS_FIRSTCLASS_INTERNATIONAL) {
            $("#rateBtn").show();
        }else {
            $("#rateBtn").hide();
        }
    })

    //USPS PRIORITY INTERNATIONAL SERVICE FOR RATES CALL 
    function checkService(){
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        if(service == shippingServiceCodes.USPS_PRIORITY_INTERNATIONAL) {
            return  getUspsPriorityIntlRates();
        }
    }

    function getUspsPriorityIntlRates(){
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        var descpall = []; var qtyall = []; var valueall = [];
        $.each($(".descp"), function(){
            if(!($(this).val()) == '') {
                descpall.push($(this).val());
            }
        });
        $.each($(".quantity"), function(){
            if(!($(this).val()) == '') {
                qtyall.push($(this).val());
            }
        });
        $.each($(".value"), function(){
            if(!($(this).val()) == '') {
                valueall.push($(this).val());
            }
        });
        if(descpall.length && qtyall.length && valueall.length) {
            $('#loading').fadeIn();
            $.get('{{ route("api.usps_rates") }}',{
                    service: service,
                    order_id: order_id,
                    descp: descpall,
                    qty: qtyall,
                    value: valueall,
                }).then(function(response){
                    console.log(response);
                    if(response.success == true){
                        $('#user_declared_freight').val(response.total_amount);
                        $('#user_declared_freight').prop('readonly', true);
                        $("#uspsVal").text('$' + response.total_amount);
                        $('#uspsModal').modal('show');
                        $("#uspsAccept").click(function(){        
                            $("#order-form").submit();
                        });
                    }
                    $('#loading').fadeOut();

                }).catch(function(error){
                    console.log('error');
                    console.log(error);
                    $('#loading').fadeOut();
            })
        }else {
            alert('Add items to get rates!');
        }    
    }

    $('#us_shipping_service').ready(function() {
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        if(service == shippingServiceCodes.USPS_PRIORITY || service == shippingServiceCodes.USPS_FIRSTCLASS) {

        //   return  getUspsRates();

        } else if(service == shippingServiceCodes.UPS_GROUND) {

            // return getUpsRates();

        } else if(service == shippingServiceCodes.FEDEX_GROUND)
        {
            // return getFedExRates();
        }
        
    })

    $('#us_shipping_service').on('change',function(){
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        
        if(service == shippingServiceCodes.USPS_PRIORITY || service == shippingServiceCodes.USPS_FIRSTCLASS) {

           return getUspsRates();

        }else if(service == shippingServiceCodes.FEDEX_GROUND)
        {
            return getFedExRates();
            
        } else if(service == shippingServiceCodes.UPS_GROUND) {
            return getUpsRates();
        }
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
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
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

    function getUpsRates(){
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        
        $('#loading').fadeIn();
        $.get('{{ route("api.ups_rates") }}',{
                service: service,
                order_id: order_id,
            }).then(function(response){
                if(response.success == true){
                    $('#user_declared_freight').val(response.total_amount);
                    $('#user_declared_freight').prop('readonly', true);
                }
                if(response.success == false)
                {
                    toastr.error(response.error);
                    $('#ups_response').css('display', 'block');
                    $('#ups_response').empty().append(response.error);
                }
                $('#loading').fadeOut();

            }).catch(function(error){
                console.log(error);
                $('#loading').fadeOut();
        })
    }

    function getFedExRates()
    {
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();

        $('#loading').fadeIn();
        $.get('{{ route("api.fedExRates") }}',{
                service: service,
                order_id: order_id,
            }).then(function(response){
                if(response.success == true){
                    $('#user_declared_freight').val(response.total_amount);
                    $('#user_declared_freight').prop('readonly', true);
                }
                if(response.success == false)
                {
                    toastr.error(response.error);
                    $('#fedex_response').css('display', 'block');
                    $('#fedex_response').empty().append(response.error);
                }
                $('#loading').fadeOut();

            }).catch(function(error){
                console.log(error);
                $('#loading').fadeOut();
        })
    }
</script>
@endsection