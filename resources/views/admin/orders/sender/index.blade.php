@extends('admin.orders.layouts.wizard')
@section('wizard-css')
    <link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
@section('wizard-form')
    <form action="{{ route('admin.orders.sender.store', $order) }}" method="POST" class="wizard">
        @csrf
        <div class="content clearfix">
            <!-- Step 1 -->
            <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">@lang('orders.sender.Step 1')</h6>
            <fieldset id="steps-uid-0-p-0" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current bg-white"
                aria-hidden="false">
                <div class="row mb-1">
                    <div class="col-md-3 form-group">
                        <label for="country">@lang('orders.sender.Select Country')</label>
                        <select id="country" name="sender_country_id"
                            class="form-control countrySelect selectpicker show-tick" data-live-search="true">
                            <option value="">Select @lang('address.Country')</option>
                            @foreach (countries() as $country)
                                <option
                                    {{ old('sender_country_id', __default($order->sender_country_id, optional($order->user)->country_id) ? __default($order->sender_country_id, optional($order->user)->country_id) : 30) == $country->id ? 'selected' : '' }}
                                    value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('sender_country_id')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="lastName1">@lang('orders.sender.Last Name')</label>
                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name',__default($order->sender_last_name,optional($order->user)->last_name)) }}">
                        @error('last_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Email')</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email',__default($order->sender_email,null)) }}">
                        @error('email')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Phone')<span class="text-danger" id="phone" style="display: none;">*</span></label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone',__default($order->sender_phone,null)) }}">
                        @error('phone')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6" id="tax_id">
                    <div class="form-group">
                        <label for="emailAddress1">@lang('orders.sender.Tax Id')</label>
                        <input type="text" class="form-control" name="taxt_id" value="{{ old('tax_id',__default($order->sender_taxId,null)) }}">
                        @error('taxt_id')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6 d-none" id="state">
                    <div class="form-group">
                        <label for="sender_state">@lang('orders.sender.State')<span class="text-danger">*</span></label>
                        <option value="" selected disabled hidden>Select State</option>
                        <select name="sender_state_id" id="sender_state" class="form-control selectpicker show-tick" data-live-search="true" required>
                            <option value="">Select @lang('address.State')</option>
                            @foreach ($states as $state)
                                <option {{ old('sender_state_id', __default($floridaStateId, optional($order)->sender_state_id)) == $state->id ? 'selected' : '' }} value="{{ $state->id }}">{{ $state->code }}</option>
                            @endforeach
                        </select>
                        @error('sender_state_id')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6" id="address" style="display: none">
                    <div class="form-group">
                        <label for="sender_address">@lang('orders.sender.Address')<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_address" name="sender_address" value="{{ old('sender_address',__default($order->sender_address,optional($order->user)->address)) }}">
                        @error('taxt_id')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6" id="city" style="display: none">
                    <div class="form-group">
                        <label for="sender_city">@lang('orders.sender.City')<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_city" name="sender_city" value="{{ old('sender_city',__default($order->sender_city,optional($order->user)->city)) }}">
                        @error('taxt_id')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6 d-none" id="zip_code">
                    <div class="form-group">
                        <label for="zipcode">@lang('orders.sender.Zipcode')<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sender_zipcode" name="sender_zipcode" value="{{ old('sender_zipcode',__default($order->sender_zipcode,optional($order->user)->zipcode)) }}">
                        @error('sender_zipcode')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="help-block" id="zipcode_response"></div>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="actions clearfix">
        <ul role="menu" aria-label="Pagination">
            <li class="disabled" aria-disabled="true">
                {{-- <a href="{{ route('admin.orders.packages.index') }}" role="menuitem">Previous</a> --}}
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button class="btn btn-primary">@lang('orders.sender.Next')</button>
            </li>
        </ul>
    </div>
</form>
@endsection

@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
<script>
    $(document).ready(function(){

        var countryChile = {!! $countryChile !!};
        var countryUS = {!! $countryUS !!};

        
        $('.selectpicker').prop('disabled', false);
        $('.selectpicker').selectpicker('refresh');
        $("[name='sender_address']").prop( "disabled", true );
        $("[name='sender_city']").prop('disabled',true);

        let selected = $('#country').val();

        if(selected == countryChile || selected == countryUS) {
                $('#address').css('display', 'block');
                $('#city').css('display', 'block');
                $('#tax_id').css('display', 'none');
                $('#phone').css('display', 'inline-block');
                $('#sender_state').prop('disabled', true);

                $("[name='sender_address']").prop( "disabled", false );
                $("[name='sender_city']").prop('disabled',false);
                $("[name='taxt_id']").prop('disabled', true);

                $("[name='sender_address']").prop('required',true);
                $("[name='sender_city']").prop('required',true);
                if (selected == countryChile) {
                    $("[name='phone']").prop('required',true);
                }
               
                $("[name='sender_zipcode']").prop('required', false);

                if (selected == countryUS) {
                    $('#state').removeClass('d-none');
                    $('#zip_code').removeClass('d-none');

                    $('#state').addClass('d-block');
                    $('#zip_code').addClass('d-block');

                    $('#sender_state').prop('required', true);
                    $("[name='sender_zipcode']").prop('required', true);

                    $('#sender_state').prop('disabled', false);

                    $("[name='phone']").prop('required',false);
                }
        } else 
        {
                $('#address').css('display', 'none');
                $('#city').css('display', 'none'); 
                $('#tax_id').css('display', 'block');
                $('#phone').css('display', 'none');
                $('#state').addClass('d-none');
                $('#zip_code').addClass('d-none');

                $('#state').removeClass('d-block');
                $('#zip_code').removeClass('d-block');
            
                $("[name='taxt_id']").prop('disabled', false);
                $('#sender_state').prop('disabled', true);
        }

        $('#sender_address').on('change', function(){
            window.validate_us_address();
        });
        
        $('#country').change(function () {
            let selected = $('#country').val();
            
            if(selected == countryChile || selected == countryUS) {
                $('#address').css('display', 'block');
                $('#city').css('display', 'block');
                $('#tax_id').css('display', 'none'); 
                $('#phone').css('display', 'inline-block');
                $('#sender_state').prop('disabled', true);

                if (selected == countryUS) {
                    $('#state').removeClass('d-none');
                    $('#zip_code').removeClass('d-none');

                    $('#state').addClass('d-block');
                    $('#zip_code').addClass('d-block');

                    $('#sender_state').prop('required',true);
                    $("[name='sender_zipcode']").prop('required', true);
                    
                    $('#sender_state').prop('disabled',false);

                    let senderAddress = $('#sender_address').val();
                    let senderCity = $('#sender_city').val();
                    let senderZipcode = $('#sender_zipcode').val();

                    if (senderAddress == undefined || senderAddress == '') {
                        $('#sender_address').val('2200 NW 129TH AVE');
                    }

                    if (senderCity == undefined || senderCity == '') {
                        $('#sender_city').val('Miami');
                    }

                    if (senderZipcode == undefined || senderZipcode == '') {
                        $('#sender_zipcode').val('33182');
                    }

                    window.validate_us_address();
                }

                $("[name='sender_address']").prop( "disabled", false );
                $("[name='sender_city']").prop('disabled',false);
                $("[name='taxt_id']").prop('disabled', true);

                $("[name='sender_address']").prop('required',true);
                $("[name='sender_city']").prop('required',true);
                if (selected == countryChile) {
                    $("[name='phone']").prop('required',true);
                }
                
            } else {
                $('#address').css('display', 'none');
                $('#city').css('display', 'none');
                $('#tax_id').css('display', 'block');
                $('#phone').css('display', 'none');
                $('#state').addClass('d-none');
                $('#zip_code').addClass('d-block');

                //$('#state').removeClass('d-block');
                //$('#zip_code').removeClass('d-block');

                $("[name='sender_address']").prop('disabled', true);
                $("[name='sender_city']").prop('disabled', true);
                $("[name='taxt_id']").prop('disabled', false);

                $("[name='sender_address']").prop('required', false);
                $("[name='sender_city']").prop('required', false);
                $('#sender_state').prop('required', false);
                $("[name='phone']").prop('required', false);

                $('#sender_state').prop('disabled', true);
            }

            $('#sender_address').on('change', function() {
                window.validate_us_address();
            });

            $('#country').change(function() {
                let selected = $('#country').val();

                if (selected == '46' || selected == '250') {
                    $('#address').css('display', 'block');
                    $('#city').css('display', 'block');
                    $('#tax_id').css('display', 'none');
                    $('#phone').css('display', 'inline-block');
                    $('#sender_state').prop('disabled', true);

                    if (selected == '250') {
                        $('#state').removeClass('d-none');
                        $('#zip_code').removeClass('d-none');

                        $('#state').addClass('d-block');
                        $('#zip_code').addClass('d-block');

                        $('#sender_state').prop('required', true);
                        $("[name='sender_zipcode']").prop('required', true);

                        $('#sender_state').prop('disabled', false);

                        window.validate_us_address();
                    }

                    $("[name='sender_address']").prop("disabled", false);
                    $("[name='sender_city']").prop('disabled', false);
                    $("[name='taxt_id']").prop('disabled', true);

                    $("[name='sender_address']").prop('required', true);
                    $("[name='sender_city']").prop('required', true);
                    $("[name='phone']").prop('required', true);
                } else {
                    $('#address').css('display', 'none');
                    $('#city').css('display', 'none');
                    $('#tax_id').css('display', 'block');
                    $('#phone').css('display', 'none');
                    $('#state').addClass('d-none');
                    $('#zip_code').addClass('d-none');

                    $('#state').removeClass('d-block');
                    $('#zip_code').removeClass('d-block');

                    $("[name='sender_address']").prop('disabled', true);
                    $("[name='sender_city']").prop('disabled', true);
                    $("[name='taxt_id']").prop('disabled', false);

                    $("[name='sender_address']").prop('required', false);
                    $("[name='sender_city']").prop('required', false);
                    $('#sender_state').prop('required', false);
                    $("[name='phone']").prop('required', false);
                    $("[name='sender_zipcode']").prop('required', false);

                    $('#sender_state').prop('disabled', true);
                }
            });

            $('#sender_state').on('change', function() {
                window.validate_us_address();
            });

            $('#sender_city').on('change', function() {
                console.log('city changed');
                window.validate_us_address();
            });

        })

        validate_us_address = function() {
            let country = $('#country').val();
            let address = $('#sender_address').val();
            let state = $('#sender_state option:selected').text();
            let city = $('#sender_city').val();

            if (country == '250' && state != undefined && address.length > 4 && city.length >= 4) {
                $('#loading').fadeIn();
                $.get('{{ route('api.orders.recipient.us_address') }}', {
                    address: address,
                    state: state,
                    city: city,
                }).then(function(response) {

                    if (response.success == true && response.zipcode != 0) {
                        $('#loading').fadeOut();
                        $('#sender_zipcode').val(response.zipcode);
                        $('#zipcode_response').empty().append(
                            "<p><b>According to your given Address, your zip code should be this</b></p><p><span style='color: red;'>Zipcode: </span><span>" +
                            response.zipcode + "</span></p>");
                    } else {
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append(
                            "<p style='color: red;'><b>According to USPS,</b></p><p><span style='color: red;'></span><span>" +
                            response.message + "</span></p>");
                    }

                }).catch(function(error) {
                    console.log(error);
                    $('#loading').fadeOut();
                    $('#zipcode_response').empty().append(
                        "<p style='color: red;'><b>According to USPS, your address is Invalid</b></p>");
                })
            }
        }
    })
    </script>
@endsection
