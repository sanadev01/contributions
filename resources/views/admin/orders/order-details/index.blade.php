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
<form action="{{ route('admin.orders.order-details.store',$order) }}" method="POST" class="wizard" id="order-form">
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
                        <input name="customer_reference" class="form-control" {{($order->recipient->country_id == $chileCountryId) ? 'required' : ''}} value="{{ $order->customer_reference }}" placeholder="@lang('orders.order-details.Customer Reference')" />
                        <p class="text-danger">{{ $errors->first('customer_reference') }}</p>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.order-details.WHR')# <span class="text-danger"></span></label>
                        <input class="form-control" readonly value="{{ $order->warehouse_number }}" placeholder="@lang('orders.order-details.Warehouse Number')" />
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label class="h4">Freight <span class="text-danger"></span></label>
                        <input class="form-control" name="user_declared_freight" id="user_declared_freight" value="{{ old('user_declared_freight', $order->user_declared_freight) }}" placeholder="Freight" />
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
                        @if ($order->recipient->country_id != $usCountryId)
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="shipping_service_id" id="shipping_service_id" required placeholder="Select Shipping Service">
                            <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                            @foreach ($shippingServices as $shippingService)
                            <option value="{{ $shippingService->id }}" {{ old('shipping_service_id',$order->shipping_service_id) == $shippingService->id ? 'selected' : '' }} data-cost="{{$shippingService->getRateFor($order)}}" data-services-cost="{{ $order->services()->sum('price') }}" data-service-code="{{$shippingService->service_sub_class}}">@if($shippingService->getRateFor($order)){{ "{$shippingService->sub_name} - $". $shippingService->getRateFor($order) }}@else{{ $shippingService->sub_name }}@endif</option>
                            @endforeach
                        </select>
                        @else
                        {{-- for usps,ups and fedex --}}
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="shipping_service_id" id="us_shipping_service" required placeholder="Select Shipping Service">
                            <option value="">@lang('orders.order-details.Select Shipping Service')</option>
                            @foreach ($shippingServices as $shippingService)
                            @if($shippingService->is_inbound_domestic_service)
                            <option value="{{ $shippingService->id }}" {{ old('shipping_service_id',$order->shipping_service_id) == $shippingService->id ? 'selected' : '' }} data-cost="{{$shippingService->getRateFor($order)}}" data-services-cost="{{ $order->services()->sum('price') }}" data-service-code="{{$shippingService->service_sub_class}}">@if($shippingService->getRateFor($order)){{ "{$shippingService->sub_name} - $". $shippingService->getRateFor($order) }}@else{{ $shippingService->sub_name }}@endif</option>
                            @else
                            <option value="{{ $shippingService->id }}" {{ old('shipping_service_id',$order->shipping_service_id) == $shippingService->id ? 'selected' : '' }} data-service-code="{{$shippingService->service_sub_class}}">{{ "{$shippingService->sub_name}"}}</option>
                            @endif
                            @endforeach
                        </select>
                        @endif
                        <div class="help-block"></div>
                    </div>
                </div>
                {{-- @dd($order->tax_modality ) --}}
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.order-details.Tax Modality') <span class="text-danger"></span></label>
                        <select class="form-control selectpicker show-tick" name="tax_modality" id="tax_modality" readonly required placeholder="@lang('orders.order-details.Tax Modality')">
                            <option value="ddu" {{ 'ddu' == $order->tax_modality ? 'selected' : '' }}>DDU</option>
                            <option value="ddp" {{ 'ddp' == $order->tax_modality || setting('prc_label', null, $order->user->id) ? 'selected' : '' }}>DDP</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="col-md-8">
                @if($order->sinerlog_tran_id)
                <div class="controls row mb-1">
                    <div class="form-check form-check-inline mr-5">
                        <div class="vs-checkbox-con vs-checkbox-primary" title="Parcel Return to Origin">
                            <input type="checkbox" name="return_origin" id="returnParcel" @if($order->sinerlog_tran_id == 1) checked @endif>
                            <span class="vs-checkbox vs-checkbox-lg">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                        <label class="form-check-label font-medium-1 font-weight-bold mt-2 ml-2" for="returnParcel">Return parcel, I'm responsible for the Cost<span class="text-danger"></span></label>
                    </div>
                    <div class="form-check form-check-inline mr-5">
                        <div class="vs-checkbox-con vs-checkbox-primary" title="Disposal All Authorized">
                            <input type="checkbox" name="dispose_all" id="disposeAll" @if($order->sinerlog_tran_id == 2) checked @endif>
                            <span class="vs-checkbox vs-checkbox-lg">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                        <label class="form-check-label font-medium-1 font-weight-bold mt-2 ml-2" for="disposeAll">Dispose Parcel<span class="text-danger"></span></label>
                    </div>
                    {{-- <div class="form-check form-check-inline mr-5">
                        <div class="vs-checkbox-con vs-checkbox-primary" title="Choose Return by Individual Parcel">
                            <input type="checkbox" name="individual_parcel" id="returnIndividual" @if($order->sinerlog_tran_id == 3) checked @endif>
                            <span class="vs-checkbox vs-checkbox-lg">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                        <label class="form-check-label font-medium-1 font-weight-bold mt-2 ml-2" for="returnIndividual">Choose Return by Individual Parcel<span class="text-danger"></span></label>
                    </div> --}}
                </div>
                @else
                <div class="controls row mb-1">
                    <div class="form-check form-check-inline mr-5">
                        <div class="vs-checkbox-con vs-checkbox-primary" title="Parcel Return to Origin">
                            <input type="checkbox" name="return_origin" id="returnParcel" @if(setting('return_origin', null, auth()->user()->id)) checked @endif>
                            <span class="vs-checkbox vs-checkbox-lg">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                        <label class="form-check-label font-medium-1 font-weight-bold mt-2 ml-2" for="returnParcel">Return All Parcels on My Account Cost<span class="text-danger"></span></label>
                    </div>
                    <div class="form-check form-check-inline mr-5">
                        <div class="vs-checkbox-con vs-checkbox-primary" title="Disposal All Authorized">
                            <input type="checkbox" name="dispose_all" id="disposeAll" @if(setting('dispose_all', null, auth()->user()->id)) checked @endif>
                            <span class="vs-checkbox vs-checkbox-lg">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                        <label class="form-check-label font-medium-1 font-weight-bold mt-2 ml-2" for="disposeAll">Disposal All Authorized<span class="text-danger"></span></label>
                    </div>
                    {{-- <div class="form-check form-check-inline mr-5">
                        <div class="vs-checkbox-con vs-checkbox-primary" title="Choose Return by Individual Parcel">
                            <input type="checkbox" name="individual_parcel" id="returnIndividual" @if(setting('individual_parcel', null, auth()->user()->id)) checked @endif>
                            <span class="vs-checkbox vs-checkbox-lg">
                                <span class="vs-checkbox--check">
                                    <i class="vs-icon feather icon-check"></i>
                                </span>
                            </span>
                        </div>
                        <label class="form-check-label font-medium-1 font-weight-bold mt-2 ml-2" for="returnIndividual">Choose Return by Individual Parcel<span class="text-danger"></span></label>
                    </div> --}}
                </div>
                @endif
            </div>
            <div class="row col-12" id="itemLimit">
                <h5 class="content-justify text-info"><b>@lang('orders.order-details.Item Limit')</b></h5>
            </div>
            <livewire:order.order-details.order-items :order-id="$order->id" />
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
                <a href="{{ route('admin.orders.recipient.index',$order->encrypted_id) }}" role="menuitem">@lang('orders.order-details.Previous')</a>
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button type="button" class="btn btn-success" id="rateBtn" onClick="checkService()">Get Rate</button>
                <button class="btn btn-primary" id="submitButton" @if($order->items->isEmpty()) title="Please add atleast one item !" disabled @endif >@lang('orders.order-details.Place Order')</button>
            </li>
        </ul>
    </div>
</form>

<div class="modal fade" id="checkOptionsModal" tabindex="-1" role="dialog" aria-labelledby="checkOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: red;">
                <h5 class="modal-title" id="checkOptionsModalLabel" style="color:white;">Please Check Disposal Option</h5>
            </div>
            <div class="modal-body">
                You must check at least one disposal option before saving order.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--USPS PRIORITY INTERNATIONAL RATE ALERT MODAL-->
<div class="modal fade" id="uspsModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">USPS Priority Intrernational</h5>
            </div>
            <div class="modal-body">
                <h4>@lang('orders.order-details.Parcel Rate')</h4>
                <ul>
                    <li>
                        <h4>@lang('orders.order-details.Charge-msg1') <span class="badge badge-light" id="uspsVal"></span> @lang('orders.order-details.Charge-msg2')</h4>
                    </li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="uspsAccept">@lang('orders.order-details.Proceed Order')</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('orders.order-details.Decline')</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="gssRateModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Error</h5>
            </div>
            <div class="modal-body">
                <h4>Service Not Found.</h4>
                <ul>
                    Please contact HomeDeliverybr support.
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="taxModalityModal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Info</h5>
            </div>
            <div class="modal-body">
                <h5>
                    @lang('orders.DDP/PRC service not available')
                </h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info text-white" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>

<script>
    $("#rateBtn").hide();
    $("#itemLimit").hide();
    getGSSRates();
    $('#shipping_service_id').on('change', function() {
        $('#user_declared_freight').val(
            parseFloat($('option:selected', this).attr("data-cost"))
        );
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        emitSHCodes()
        if (service == 3442 || service == 3443) {
            $("#rateBtn").show();
            $("#itemLimit").hide();
        } else if (service == 477 || service == 3674 || service == 37634 || service == 3326 || service == 4367 || service == 237) {
            return getGSSRates();
        } else if (service == 238) {
            return getPasarExColombiaRates();
        }else if (service == 537 || service == 540 || service == 773) {
            $("#itemLimit").show();
            $("#rateBtn").hide();
        } else {
            $("#itemLimit").hide();
            $("#rateBtn").hide();
        }
        showTaxModalityMessage(service) 

    })
    function showTaxModalityMessage(service){
         serviceCode = Number(service)
        const uspsService = @json($uspsService);
        console.log(uspsService,serviceCode);
        if(uspsService.includes(serviceCode)){
            $('#taxModalityModal').modal('show');
        }
    }

    //USPS PRIORITY INTERNATIONAL SERVICE FOR RATES CALL 
    function checkService() {
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        showTaxModalityMessage(service)
        if (service == 3442) {
            return getUspsPriorityIntlRates();
        }
    }

    function getUspsPriorityIntlRates() {
        flag = true;
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
         var order_id = $('#order_id').val();
        var descpall = [];
        var qtyall = [];
        var valueall = [];
        $.each($(".descp"), function() {
            if (!($(this).val()) == '') {
                descpall.push($(this).val());
            }
        });
        $.each($(".quantity"), function() {
            if (!($(this).val()) == '') {
                qtyall.push($(this).val());
            }
        });
        $.each($(".value"), function() {
            if (!($(this).val()) == '') {
                valueall.push($(this).val());
            }
        });
        if (descpall.length && qtyall.length && valueall.length) {
            $('#loading').fadeIn();
            $.get('{{ route("api.usps_rates") }}', {
                service: service,
                order_id: order_id,
                descp: descpall,
                qty: qtyall,
                value: valueall,
            }).then(function(response) {
                console.log(response);
                if (response.success == true) {
                    $('#user_declared_freight').val(response.total_amount);
                    $('#user_declared_freight').prop('readonly', true);
                    $("#uspsVal").text('$' + response.total_amount);
                    $('#uspsModal').modal('show');
                    $("#uspsAccept").click(function() {
                        $("#order-form").submit();
                    });
                }
                $('#loading').fadeOut();

            }).catch(function(error) {
                console.log('error');
                console.log(error);
                $('#loading').fadeOut();
            })
        } else {
            alert('Add items to get rates!');
            flag = false;
        }
        if(flag)
            showTaxModalityMessage(service)
       
    }


    $('#us_shipping_service').ready(function() {
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        showTaxModalityMessage(service)
        if (service == 3440 || service == 3441) {

            return getUspsRates();

        } else if (service == 3) {
            return getUpsRates();
        } else if (service == 4) {
            return getFedExRates();
        }

    })

    $('#us_shipping_service').on('change', function() {
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        showTaxModalityMessage(service)
       
        if (service == 3440 || service == 3441 || service == 05) {

            return getUspsRates();

        } else if (service == 4) {
            return getFedExRates();

        } else if (service != undefined && service == 03) {
            return getUpsRates();
        }
    })

    function change(id) {
        var id = "dangrous_" + id;
        value = $('#' + id).val();
        if (value == 'contains_battery') {
            $(".dangrous").children("option[value^='contains_perfume']").hide()
        }
        if (value == 'contains_perfume') {
            $(".dangrous").children("option[value^='contains_battery']").hide()
        }
        if (value == 0) {
            $(".dangrous").children("option[value^='contains_battery']").show();
            $(".dangrous").children("option[value^='contains_perfume']").show();
        }
    }

    function getUspsRates() {
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        showTaxModalityMessage(service) 
        $('#loading').fadeIn();
        $.get('{{ route("api.usps_rates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {
            if (response.success == true) {
                $('#user_declared_freight').val(response.total_amount);
                $('#user_declared_freight').prop('readonly', true);
            }
            $('#loading').fadeOut();

        }).catch(function(error) {
            console.log(error);
            $('#loading').fadeOut();
        })

    }

    function getUpsRates() {
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();

        $('#loading').fadeIn();
        $.get('{{ route("api.ups_rates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {
            if (response.success == true) {
                $('#user_declared_freight').val(response.total_amount);
                $('#user_declared_freight').prop('readonly', true);
            }
            if (response.success == false) {
                toastr.error(response.error);
                $('#ups_response').css('display', 'block');
                $('#ups_response').empty().append(response.error);
            }
            $('#loading').fadeOut();

        }).catch(function(error) {
            console.log(error);
            $('#loading').fadeOut();
        })
    }

    function getFedExRates() {
        const service = $('#us_shipping_service option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        showTaxModalityMessage(service)

        $('#loading').fadeIn();
        $.get('{{ route("api.fedExRates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {
            if (response.success == true) {
                $('#user_declared_freight').val(response.total_amount);
                $('#user_declared_freight').prop('readonly', true);
            }
            if (response.success == false) {
                toastr.error(response.error);
                $('#fedex_response').css('display', 'block');
                $('#fedex_response').empty().append(response.error);
            }
            $('#loading').fadeOut();

        }).catch(function(error) {
            console.log(error);
            $('#loading').fadeOut();
        })
    }

    function getGSSRates() {
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();
        showTaxModalityMessage(service)

        $('#loading').fadeIn();
        $.get('{{ route("api.gssRates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {
            if (response.success == true) {

                if (service != 283) {
                    $('#user_declared_freight').val(response.total_amount);
                    $('#user_declared_freight').prop('readonly', true);
                }
                return true;
            } else {
                if (service == 3674) {
                    $('#gssRateModal').modal('show');

                    $('#shipping_service_id option:selected').remove();
                    $('#shipping_service_id').selectpicker('refresh');
                }
            }
            $('#loading').fadeOut();

        }).catch(function(error) {
            console.log(error);
            $('#loading').fadeOut();
        })
        return false;
    }

    function getPasarExColombiaRates() {
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        var order_id = $('#order_id').val();

        $('#loading').fadeIn();
        $.get('{{ route("api.pasarExRates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {
            if (response.success == true) {
                $('#user_declared_freight').val(response.total_amount);
                $('#user_declared_freight').prop('readonly', true);
            }
            $('#loading').fadeOut();

        }).catch(function(error) {
            console.log(error);
            $('#loading').fadeOut();
        })

    }

    $('#returnParcel').change(function() {
        if ($(this).is(":checked")) {
            $('#disposeAll').prop('checked', false);
            $('#returnIndividual').prop('checked', false);
        }
    });
    $('#disposeAll').change(function() {
        if ($(this).is(":checked")) {
            $('#returnParcel').prop('checked', false);
            $('#returnIndividual').prop('checked', false);
        }
    });
    $('#returnIndividual').change(function() {
        if ($(this).is(":checked")) {
            $('#returnParcel').prop('checked', false);
            $('#disposeAll').prop('checked', false);
        }
    });
    //handle shcode
    $('#shipping_service_id').ready(function() {
        emitSHCodes()
    })

    function emitSHCodes(serviceCode) {
        const service = $('#shipping_service_id option:selected').attr('data-service-code');
        if (service) {
            $('.sh_code').selectpicker('destroy');
            window.livewire.emit('loadSHCodes', {
                service: service
            });
        }
    }


    window.addEventListener('initializeSelectPicker', event => {
        initializeSelectpicker();
    })
    window.addEventListener('emitSHCodes', event => {
        emitSHCodes();
    })
    window.addEventListener('emitSHCodesLazy', event => {
        setTimeout(() => {
            emitSHCodes();
        }, 1500);
    })

    function initializeSelectpicker() {
        $('#loading').fadeIn();
        $('.sh_code').selectpicker('destroy');
        setTimeout(() => {
            $('.sh_code').selectpicker({
                liveSearch: true,
                liveSearchPlaceholder: 'Search...',
            });
            $('#loading').fadeOut();
        }, 2500);
    }
    window.addEventListener('itemAdded', event => {
        setTimeout(() => {
            emitShCodePicker()
            initializeSelectpicker()
        }, 3000);
    })

    window.addEventListener('disabledSubmitButton', event => {
        var button = document.getElementById('submitButton');
        button.setAttribute('disabled', 'disabled');
    })

    window.addEventListener('activateSubmitButton', event => {
        var button = document.getElementById('submitButton');
        button.removeAttribute('disabled');
    })

    document.addEventListener('DOMContentLoaded', function() {
        var orderForm = document.getElementById('order-form');
        var returnParcelCheckbox = document.getElementById('returnParcel');
        var disposeAllCheckbox = document.getElementById('disposeAll');
        var checkOptionsModal = document.getElementById('checkOptionsModal');

        orderForm.addEventListener('submit', function(event) {
            // Check if both checkboxes are unchecked
            if (!returnParcelCheckbox.checked && !disposeAllCheckbox.checked) {
                event.preventDefault(); // Prevent form submission
                $('#checkOptionsModal').modal('show'); // Show the modal
            }
        });
    });
</script>
@endsection