@extends('admin.orders.layouts.order-items-wizard')
@section('wizard-css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
@section('wizard-form')
@if ($error)
<div class="alert alert-danger" role="alert">
    <ul>
        @foreach (explode('!', $error) as $index=>$msg)
        @if(trim($msg))
        @if($index==0)
        @foreach (explode(':', $msg) as $index2=>$msg2)
        @if(trim($msg))
        @if($index2==0)
        <h6 class="alert text-danger text-justify">{{ trim($msg2) }}</h6>
        @else
        <li>{{ trim($msg2) }}</li>
        @endif
        @endif
        @endforeach
        @else
        <li>{{ trim($msg) }}</li>
        @endif
        @endif
        @endforeach
    </ul>
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
                <div class="form-group col-12 col-md-4">
                    <div class="controls">
                        <label class="h4 my-2">@lang('orders.order-details.Customer Reference') <span class="text-danger"></span></label>
                        <input name="customer_reference" class="form-control fs-1" {{($order->recipient->country_id == $chileCountryId) ? 'required' : ''}} value="{{ $order->customer_reference }}" placeholder="@lang('orders.order-details.Customer Reference')" />
                        <p class="text-danger">{{ $errors->first('customer_reference') }}</p>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-md-4">
                    <div class="controls">
                        <label class="h4 my-2">@lang('orders.order-details.Tax Modality') <span class="text-danger"></span></label>
                        <select class="form-control bg-white  show-tick" style="background-color: #f6f5ff !important;" name="tax_modality" id="tax_modality" readonly required placeholder="@lang('orders.order-details.Tax Modality')">
                            <option value="ddu" {{ 'ddu' == $order->tax_modality ? 'selected' : '' }}>DDU</option>
                            <option value="ddp" {{ 'ddp' == $order->tax_modality || setting('is_prc_user', null, $order->user->id) ? 'selected' : '' }}>DDP</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-md-4 me-3">
                    <div class="controls">
                        <label class="h4 my-2">Freight <span class="text-danger"></span></label>
                        <input class="form-control" name="user_declared_freight" id="user_declared_freight" value="{{ old('user_declared_freight', $order->user_declared_freight) }}" placeholder="Freight" />
                        <div class="help-block"></div>
                        <span class="text-danger">@error('user_declared_freight') {{ $message }} @enderror</span>
                    </div>
                </div>
            </div>
            <div id="error-alert" style="display: none; color: red; font-weight: bold; margin-bottom: 10px;">
                <!-- Error message will appear here -->
            </div>

            <hr>
            <div class="row mt-1">
                <div class="form-group col-12 col-md-4">
                    <div class="controls">
                        <span class="my-2" style="padding-bottom: 20px !important;">
                            <h4 class="h4 my-2">@lang('orders.order-details.Service')</h4>
                        </span>

                         <livewire:service-search :allServices="$shippingServices" :order="$order" />
 
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="form-group col-12 col-md-4">
                    <label><span class="text-danger"></span></label>
                    <div class="controls">
                        @if($order->sinerlog_tran_id)
                        <label for="h4"></label>
                        <div class="controls row mb-1">
                            <div class="form-check form-check-inline mr-5">
                                <div class="vs-checkbox-con vs-checkbox-primary" title="Parcel Return to Origin">

                                    <input type="checkbox" name="return_origin " id="returnParcel" @if($order->sinerlog_tran_id == 1) checked @endif>
                                    <span class="vs-checkbox vs-checkbox-lg">
                                        <span class="vs-checkbox--check">
                                            <i class="vs-icon feather icon-check"></i>
                                        </span>
                                    </span>
                                </div>
                                <label class="form-check-label fs-7 font-weight-bold mt-2 ml-2" for="returnParcel">Return the parcel; I'll cover the cost.<span class="text-danger"></span></label>
                            </div>
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

                        </div>
                        @endif
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="form-group col-12 col-md-4">
                    <label><span class="text-danger"></span></label>
                    <div class="controls">
                        @if($order->sinerlog_tran_id)
                        <label for="h4"></label>
                        <div class="controls row mb-1">

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
                        </div>
                        @endif
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
    </div>

    <div class="row col-12" id="itemLimit">
        <h5 class="content-justify text-info"><b>@lang('orders.order-details.Item Limit')</b></h5>
    </div>
    <div class="my-5">
        <livewire:order.order-details.order-items :order-id="$order->id" />
    </div>
    <div class="row mt-1">
        <div class="form-group col-12">
            @lang('orders.order-details.declaration')
        </div>
    </div>
    </fieldset>
    </div>

    <div class="">
        <div class="d-flex justify-content-between">
            <div>
                <button class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i>
                    <a href="{{ route('admin.orders.recipient.index',$order->encrypted_id) }}" class="text-white">@lang('orders.order-details.Previous')</a>
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-success" id="rateBtn" onClick="checkService()">Get Rate</button>
                <button class="btn btn-primary" id="submitButton" @if($order->items->isEmpty()) title="Please add atleast one item !" disabled @endif >@lang('orders.order-details.Place Order') <i class="fas fa-arrow-right"></i> </button>
            </div>
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

    function updateService(serviceId, subClass, rate = null) {

        console.log('check tax modality shipping serivce')
        showTaxModalityMessage(serviceId, subClass)
        console.log('serviceid', serviceId, 'subclass', subClass, 'rate', rate)
        if (rate) {
            $('#user_declared_freight').val(rate);
        } else {
            $('#user_declared_freight').val(0);
        }

        if (subClass == 4 || subClass == 04) {
            return getFedExRates(serviceId, subClass);
        }
        if (subClass == 3440 || subClass == 3441 || subClass == 05 || subClass == 5) {
            return getUspsRates(serviceId, subClass);
        } else if (subClass == 3) {
            return getUpsRates(serviceId, subClass);
        } else if (subClass == 3442 || subClass == 3443) {
            console.log('show rate button')
            $("#rateBtn").show();
            $("#itemLimit").hide();
        } else if (subClass == 477 || subClass == 3674 || subClass == 37634 || subClass == 3326 || subClass == 4367 || subClass == 237) {
            console.log('get gss rates')
            return getGSSRates(serviceId, subClass);
        } else if (subClass == 33175) {
            console.log('get mile rates')
            return getMileRates(serviceId, subClass);
        } else if (subClass == 238) {
            console.log('get pasarex colobia rates')
            return getPasarExColombiaRates(serviceId, subClass);
        } else if (subClass == 537 || subClass == 540 || subClass == 773) {
            console.log('itemLimit show')
            $("#itemLimit").show();
            $("#rateBtn").hide();
        } else {
            console.log('itemLimit hide')
            $("#itemLimit").hide();
            $("#rateBtn").hide();
        }
    }

    function showTaxModalityMessage(serviceId, serviceCode) {
        serviceCode = Number(serviceCode)
        const uspsService = @json($uspsService);
        if (uspsService.includes(serviceCode)) {
            $('#taxModalityModal').modal('show');
            console.log(' tax modality result: service include in usps service')
        } else {
            console.log(serviceCode, 'tax modality  result: not include in usps service')
            console.log(uspsService)
        }
    }
    //USPS PRIORITY INTERNATIONAL SERVICE FOR RATES CALL 
    function checkService() {
        const subClass = $('#data-service-code').val();
        const serviceId = $('#us_shipping_service').val();
        console.log(subClass, 'subClass on check sum')
        console.log(serviceId, 'serviceId on check sum')
        if(!serviceId&&subClass){
            alert('please first select the service.')
        }
        if (subClass == 3442) {
            return getUspsPriorityIntlRates(serviceId, subClass);
        }
        else{
            alert('invalid service.')
        }
    }

    function getUspsPriorityIntlRates(serviceId, subClass) {
        const service = subClass;
        flag = true;
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
                console.log('getUspsPriorityIntlRates response', response);
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
                console.log('getUspsPriorityIntlRates error');
                console.log(error);
                $('#loading').fadeOut();
            })
        } else {
            alert('Add items to get rates!');
            console.log(descpall.length, qtyall.length, valueall.length)
            flag = false;
        } 
    }


    function usShippingService(serviceId, serviceCode) {
        showTaxModalityMessage(serviceId, serviceCode)
    }


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

    function getUspsRates(serviceId, subClass) {
        const service = subClass;
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

    function getUpsRates(serviceId, subClass) {
        const service = subClass
        var order_id = $('#order_id').val();
        console.log('api.ups_rates', 'service', service, 'order_id', order_id)
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

    function getFedExRates(serviceId, subClass) {
        const service = subClass;
        var order_id = $('#order_id').val();

        console.log('api.fedExRates', 'service', service, 'order_id', order_id)

        $('#loading').fadeIn();
        $.get('{{ route("api.fedExRates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {

            console.log('api.fedExRates response');
            console.log(response);
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
            console.log('api.fedExRates')
            console.log(error);
            $('#loading').fadeOut();
        })
    }

    function getGSSRates(serviceId, subClass) {
        const service = subClass;
        var order_id = $('#order_id').val();

        console.log('api.gssRates', 'service', service, 'order_id', order_id)
        $('#loading').fadeIn();
        $.get('{{ route("api.gssRates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {
            console.log('api.gssRates response');
            console.log(response);
            if (response.success == true) { 
                if (service != 283) {
                    $('#user_declared_freight').val(response.total_amount);
                    $('#user_declared_freight').prop('readonly', true);
                }
                return true;
            } else {
                if (service == 3674) {
                    $('#gssRateModal').modal('show');
                    Livewire.emit('removeService');
                    $("#rateBtn").hide();
                    $("#itemLimit").hide();

                }
            }
            $('#loading').fadeOut();

        }).catch(function(error) {
            console.log('api.gssRates');
            console.log(error);
            $('#loading').fadeOut();
        })
        return false;
    }

    function getPasarExColombiaRates(serviceId, subClass) {
        const service = subClass;
        var order_id = $('#order_id').val();

        // Hide error alert at the beginning of the request
        $('#error-alert').hide();

        $('#loading').fadeIn();
        $.get('{{ route("api.pasarExRates") }}', {
            service: service,
            order_id: order_id,
        }).then(function(response) {
            console.log('api.pasarExRates response');
            console.log(response);
            if (response.success == true) {
                $('#user_declared_freight').val(response.total_amount);
                $('#user_declared_freight').prop('readonly', true);
            } else {
                $('#error-alert').text(response.error).fadeIn();
                $("#rateBtn").hide();
                $("#itemLimit").hide();
                Livewire.emit('removeService');
                
                $('#user_declared_freight').val(0);
                setTimeout(function() {
                    $('#error-alert').fadeOut();
                }, 10000);
            }
            $('#loading').fadeOut();

        }).catch(function(error) {
            console.log(error);
            $('#loading').fadeOut();
        });
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


    function emitSHCodes(serviceId, subClass) {
        const service =subClass;
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
    document.addEventListener('livewire:load', function() {
        Livewire.on('service:updated', function(serviceId, subClass, rate = null) {
            console.log('Service updated with ID:', serviceId);
            console.log('Service updated with service code:', subClass);
            console.log('Service updated with rate:', rate);
            updateService(serviceId, subClass, rate);
            emitSHCodes(serviceId, subClass);
            

        });
    });
</script>
@endsection