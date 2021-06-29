@extends('admin.orders.layouts.wizard')
@section('wizard-css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
<style>
    p{margin-bottom: 0px;}
</style>
@endsection
@section('wizard-form')
<div class="card-body">
    @if( $errors->count() )
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>
                        {!! $error !!}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.orders.recipient.store',$order) }}" class="wizard" method="post" enctype="multipart/form-data">
        @csrf

        <div>
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-6">
                    <div class="controls">
                        <label>@lang('orders.recipient.slect-from-list') <span class="text-danger"></span></label>
                        <select class="form-control selectpicker show-tick" data-live-search="true" name="address_id" id="address_id" placeholder="@lang('orders.recipient.slect-from-list')">
                            <option value="">@lang('orders.recipient.slect-from-list')</option>
                            @foreach (auth()->user()->addresses()->orderBy('first_name')->get() as $address)
                                <option value="{{ $address->id }}" {{ $address->id == $order->recipient_address_id ? 'selected' : '' }}>{{ "{$address->first_name} {$address->last_name} | {$address->email} | {$address->address} {$address->address2} | {$address->street_no} | {$address->city} | {$address->zipcode} | {$address->tax_id}" }}</option>
                            @endforeach
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Type') <span class="text-danger">*</span></label>
                        <select class="form-control" name="account_type" id="accountType" required placeholder="@lang('address.Type')">
                            <option value="">@lang('address.Type')</option>
                            <option value="individual" {{ optional($order->recipient)->account_type == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="business" {{ optional($order->recipient)->account_type == 'business' ? 'selected' : '' }}>Business</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <div class="row mt-1">
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.First Name') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="first_name" value="{{old('first_name',optional($order->recipient)->first_name)}}"  placeholder="@lang('address.First Name')">
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Last Name') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="last_name" value="{{old('last_name',optional($order->recipient)->last_name)}}" placeholder="@lang('address.Last Name')">
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Email') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="email" value="{{old('email',optional($order->recipient)->email)}}" required placeholder="@lang('address.Email')">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Phone')</label>
                        <input type="text" class="form-control" name="phone" value="{{old('phone',optional($order->recipient)->phone)}}" required placeholder="+55123456789">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Address') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" value="{{old('address',optional($order->recipient)->address)}}" required placeholder="@lang('address.Address')"/>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Address')2</label>
                        <input type="text" class="form-control"  placeholder="@lang('address.Address')2" value="{{old('address2',optional($order->recipient)->address2)}}"  name="address2">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Street No')</label>
                        <input type="text" class="form-control" placeholder="@lang('address.Street No')" value="{{old('street_no',optional($order->recipient)->street_no)}}"  name="street_no" id="street_no">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="form-group">
                        <div class="controls">
                            <label>@lang('address.Country') <span class="text-danger">*</span></label>
                            <select id="country"  name="country_id" class="form-control selectpicker show-tick" data-live-search="true">
                                <option value="">Select @lang('address.Country')</option>
                                @foreach (countries() as $country)
                                    <option {{ old('country_id',optional($order->recipient)->country_id) == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <div class="help-block"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls" id="div_state" style="display: none">
                        <label>@lang('address.State') <span class="text-danger">*</span></label>
                        <select name="state_id" id="state" class="form-control selectpicker show-tick" data-live-search="true">
                            <option value="">Select @lang('address.State')</option>
                            @foreach (states() as $state)
                                <option value="{{ $state->id }}" {{ old('state_id',optional($order->recipient)->state_id) == $state->id ? 'selected' : '' }}> {{ $state->code }} </option>
                            @endforeach
                        </select>
                        <div class="help-block"></div>
                    </div>
                    {{-- Chile Regions --}}
                    <div class="controls" id="div_region" style="display: none">
                        <label>Regions <span class="text-danger">*</span></label>
                        <select name="state_id" id="region" class="form-control selectpicker show-tick" data-live-search="true" data-value="{{ old('state_id', optional($order->recipient)->state_id) }}">
                            <option value="">Select Region</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 offset-4">
                    <div class="controls">
                        <div class="help-block" id="regions_response">
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls" id="div_city" style="display: none">
                        <label>@lang('address.City') <span class="text-danger">*</span></label>
                        <input type="text" id="city" name="city" value="{{old('city',optional($order->recipient)->city)}}" class="form-control"  required placeholder="City"/>
                        <div class="help-block"></div>
                    </div>
                    {{-- Chile Communes --}}
                    <div class="controls" id="div_communes" style="display: none">
                        <label>Communes <span class="text-danger">*</span></label>
                        <select name="city" id="commune" class="form-control selectpicker show-tick" data-live-search="true" data-value="{{ old('city', optional($order->recipient)->city) }}">
                            <option value="">Select Commune</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                    <div class="controls">
                        <div class="help-block" id="communes_response" style="display: none">
                        </div>
                    </div>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Zip Code')</label>
                        <input type="text" name="zipcode"  id="zipcode" value="{{ cleanString(old('zipcode',optional($order->recipient)->zipcode)) }}" required class="form-control" placeholder="Zip Code"/>
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-4" id="cpf">
                    <div class="controls">
                            <label id="cnpj_label_id" style="{{ optional($order->recipient)->account_type != 'individual' ? 'display:block' : 'display:none' }}" >@lang('address.CNPJ') <span class="text-danger">* (Brazil Only)</span> </label>
                            <label id="cpf_label_id" style="{{ optional($order->recipient)->account_type == 'individual' ? 'display:block' : 'display:none' }}" >@lang('address.CPF') <span class="text-danger">* (Brazil Only)</span> </label>
                        <input type="text" name="tax_id" id="tax_id" value="{{old('tax_id',optional($order->recipient)->tax_id)}}" class="form-control" placeholder="CNPJ"/>
                        <div class="help-block"></div>
                    </div>
                </div>
                
                <div class="form-group col-12 offset-4">
                    <div class="controls">
                        <div class="help-block" id="zipcode_response">
                        </div>
                    </div>
                </div>

                <div class="col-12 my-3 p-4 ">
                    <div class="row justify-content-end">
                        <fieldset class="col-md-4 text-right">
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" name="save_address" value="false">
                                <span class="vs-checkbox vs-checkbox-lg">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="h3 mx-2 text-primary my-0 py-0">@lang('address.save Address')</span>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="actions clearfix">
            <ul role="menu" aria-label="Pagination">
                <li class="disabled" aria-disabled="true">
                    <a href="{{ route('admin.orders.sender.index',$order) }}" role="menuitem">@lang('orders.recipient.Previous')</a>
                </li>
                <li aria-hidden="false" aria-disabled="false">
                    <button class="btn btn-primary">@lang('orders.recipient.Next')</button>
                </li>
            </ul>
        </div>
    </form>
</div>
@endsection

@section('js')
<script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
@include('layouts.states-ajax')

<script>
    $(document).ready(function(){
        $('#accountType').on('change', function(){
            let val = $(this).val();
            if(val == 'individual'){
                $('#cpf_label_id').css('display', 'block')
                $('#cnpj_label_id').css('display', 'none')
                $('#tax_id').attr('placeholder', 'CPF')
            }else{
                $('#cpf_label_id').css('display', 'none')
                $('#cnpj_label_id').css('display', 'block')
                $('#tax_id').attr('placeholder', 'CNPJ')
            }
        })
    })

    $('#address_id').on('change',function(){
        if ( $(this).val() == undefined || $(this).val() == "" ) return;
        $('#loading').fadeIn();
        $.post('{{ route("api.orders.recipient.update") }}',{
            address_id: $(this).val(),
            order_id: {{ $order->id }}
        })
        .then(function(response){
            if ( response.success ){
                window.location.reload();
            }else{
                $('#loading').fadeOut();
                toastr.error(response.message)
            }

        }).catch(function(error){
            $('#loading').fadeOut();
        })
    })
    
    $('#zipcode').on("change", function(){
        let country_id = $("#country").val();
        if(country_id == '30')
        {
            if ( $(this).val() == undefined || $(this).val() == "" ) return;
            $('#loading').fadeIn();
            $.get('{{ route("api.orders.recipient.zipcode") }}',{
                zipcode: $(this).val(),
            })
            .then(function(response){
                console.log(response.data);
                if ( response.success ){
                    $('#loading').fadeOut();
                    $('#zipcode_response').empty().append("<p><b>According to your zipcode, your address should be this</b></p><p><span style='color: red;'>Address: </span><span>"+response.data.street+"</span></p><p><span style='color: red;'>City: </span><span>"+response.data.city+"</span></p><p><span style='color: red;'>State: </span><span>"+response.data.uf+"</span></p>");
                }else{
                    $('#loading').fadeOut();
                    $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                    toastr.error(response.message)
                }
            
            }).catch(function(error){
                $('#loading').fadeOut();
            })
        }
    })

    $(document).ready(function(){
        let old_city = $('#commune').data('value');
        // For getting Chile Regions
        $('#country').ready(function() {
            $('#regions_response').css('display', 'none');
            let val = $('#country').val();
            const old_state_id = $('#region').data('value');

            if(val == '46'){
                $('#cpf').css('display', 'none')
                $('#div_state').css('display', 'none')
                $('#div_city').css('display', 'none')
                
                $('#div_region').css('display', 'block')
                $('#div_communes').css('display', 'block')

                $('#state').prop('disabled', true);
                $('#city').prop('disabled', true);

                $('#region').prop('disabled', false);
                $('#commune').prop('disable', false);

                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.chile_regions") }}')
                .then(function(response){
                    if(response.success == true)
                    {
                        $('#region').attr('disabled', false);
                        $.each(response.data,function(key, value)
                        {
                            $('#region').append('<option value="'+value.Identificador+'">'+value.Nombre+'</option>');
                            $('#region').selectpicker('refresh');
                            if(old_state_id != undefined || old_state_id != '')
                            {
                                $('#region').val(old_state_id);
                            }
                        });
                        $('#loading').fadeOut();
                    }else{
                        $('#loading').fadeOut();
                        $('#regions_response').css('display', 'block');
                        $('#regions_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                    
                }).catch(function(error){
                    console.log(error);
                })
                // Fetch Communes
                if(old_state_id != undefined || old_state_id != '')
                {
                    $('#loading').fadeIn();
                    $('#communes_response').css('display', 'none');
                    $.get('{{ route("api.orders.recipient.chile_comunes") }}',{
                        region_code: old_state_id,
                    })
                    .then(function(response){
                        if(response.success == true)
                        {
                            $('#commune').attr('disabled', false);
                            $.each(response.data,function(key, value)
                            {
                                $('#commune').append('<option value="'+value.NombreComuna+'">'+value.NombreComuna+'</option>');
                                $('#commune').selectpicker('refresh');
                                if(old_city != undefined || old_city != '')
                                {
                                    $('#commune').val(old_city);
                                }
                            });
                            $('#loading').fadeOut();
                        }else{
                            $('#loading').fadeOut();
                            $('#communes_response').css('display', 'block');
                            $('#communes_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                            toastr.error(response.message)
                        }
                    }).catch(function(error){
                        console.log(error);
                    })
                }    

            }else {
                $('#cpf').css('display', 'block')
                $('#div_state').css('display', 'block')
                $('#div_city').css('display', 'block')

                $('#div_region').css('display', 'none')

                $('#state').prop('disabled', false);
                $('#city').prop('disabled', false);

                $('#region').prop('disabled', true);
                $('#commune').prop('disable', true);
            }
        });

        $('#country').on('change', function(){
            $('#regions_response').css('display', 'none');
            let val = $(this).val();
            const old_state_id = $('#region').data('value');

            if(val == '46'){
                $('#cpf').css('display', 'none')
                $('#div_state').css('display', 'none')
                $('#div_city').css('display', 'none')

                $('#div_region').css('display', 'block')
                $('#div_communes').css('display', 'block')

                $('#state').prop('disabled', true);
                $('#city').prop('disabled', true);

                $('#region').prop('disabled', false);
                $('#commune').prop('disable', false);

                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.chile_regions") }}')
                .then(function(response){
                    if(response.success == true)
                    {
                        $('#region').attr('disabled', false);
                        $.each(response.data,function(key, value)
                        {
                            $('#region').append('<option value="'+value.Identificador+'">'+value.Nombre+'</option>');
                            $('#region').selectpicker('refresh');
                            if(old_state_id != undefined || old_state_id != '')
                            {
                                $('#region').val(old_state_id);
                            }
                        });
                    }else {
                        $('#loading').fadeOut();
                        $('#regions_response').css('display', 'block');
                        $('#regions_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                   console.log(error);
                })
                // Fetch Communes
                if(old_state_id != undefined || old_state_id != '')
                {
                    $('#loading').fadeIn();
                    $('#communes_response').css('display', 'none');
                    $.get('{{ route("api.orders.recipient.chile_comunes") }}',{
                        region_code: old_state_id,
                    })
                    .then(function(response){
                        if(response.success == true)
                        {
                            $('#commune').attr('disabled', false);
                            $.each(response.data,function(key, value)
                            {
                                $('#commune').append('<option value="'+value.NombreComuna+'">'+value.NombreComuna+'</option>');
                                $('#commune').selectpicker('refresh');
                            });
                            if(old_city != undefined || old_city != '')
                            {
                                $('#commune').val(old_city);
                            }
                            $('#loading').fadeOut();
                        }else{
                            $('#loading').fadeOut();
                            $('#communes_response').css('display', 'block');
                            $('#communes_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                            toastr.error(response.message)
                        }
                    }).catch(function(error){
                        console.log(error);
                    })
                }

            }else {
                $('#cpf').css('display', 'block')
                $('#div_state').css('display', 'block')
                $('#div_city').css('display', 'block')

                $('#div_region').css('display', 'none')
                $('#div_communes').css('display', 'none')

                $('#state').prop('disabled', false);
                $('#city').prop('disabled', false);

                $('#region').prop('disabled', true);
                $('#commune').prop('disable', true);
            }
        });

        // For getting Chile Communes based on selected region
        $('#region').on('change', function(){
            const old_state_id = $('#region').data('value');
            $('#communes_response').css('display', 'none');
            if ( $(this).val() == undefined || $(this).val() == "" ) return;
            let region_code = $('#region').val();
            
            $('#loading').fadeIn();
            $.get('{{ route("api.orders.recipient.chile_comunes") }}',{
                region_code: $(this).val(),
            })
            .then(function(response){
                if(response.success == true)
                {
                    $('#commune').attr('disabled', false);
                    $.each(response.data,function(key, value)
                    {
                        $('#commune').append('<option value="'+value.NombreComuna+'">'+value.NombreComuna+'</option>');
                        $('#commune').selectpicker('refresh');
                    });
                    if((old_state_id != undefined || old_state_id != '') && (old_city != undefined || old_city != '') && region_code == old_state_id)
                    {
                        $('#commune').val(old_city);
                    }else{
                        $('#commune').val('');
                    }
                    $('#loading').fadeOut();
                }else{
                    $('#loading').fadeOut();
                    $('#communes_response').css('display', 'block');
                    $('#communes_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                    toastr.error(response.message)
                }
            }).catch(function(error){
                console.log(error);
            })
        });

        // For validating Address and Zipcode
        $('#commune').on('change', function(){
            let commune = $(this).val();
            let address = $('#address').val();
            let street_no = $('#street_no').val();
            let country = $('#country').val();
            let direction = address.concat(" ",street_no);
            
            if ( address == undefined || address == "" || street_no == undefined || street_no == "" ) return;

            if(country == '46')
            {
                $('#loading').fadeIn();

                $.get('{{ route("api.orders.recipient.normalize_address") }}',{
                    coummne: commune,
                    direction: direction,
                })
                .then(function(response){
                    if ( response.success == true ){
                        $('#loading').fadeOut();
                        $('#zipcode').val(response.data.CodigoPostal);
                        $('#zipcode_response').empty().append("<p><b>According to your Coummune, your zipcode should be this</b></p><p><span style='color: red;'>zipcode: </span><span>"+response.data.CodigoPostal);
                    }else{
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                    console.log(error);
                })
            }
        });

        $('#address').on('change', function(){
            let address = $(this).val();
            let country = $('#country').val();
            let commune = $('#commune').val();
            let street_no = $('#street_no').val();
            let direction = address.concat(" ",street_no);

            if(country == '46' && commune != undefined && commune != "" && address.length > 5 && street_no.length > 0 && direction.length > 5)
            {
                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.normalize_address") }}',{
                    coummne: commune,
                    direction: direction,
                })
                .then(function(response){
                    if ( response.success == true ){
                        $('#zipcode').val(response.data.CodigoPostal);
                        $('#zipcode_response').empty().append("<p><b>According to your Coummune, your zipcode should be this</b></p><p><span style='color: red;'>zipcode: </span><span>"+response.data.CodigoPostal);
                        $('#loading').fadeOut();
                    }else{
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                    console.log(error);
                })
            }
        });

        $('#street_no').on('change', function(){
            let address = $('#address').val();
            let country = $('#country').val();
            let commune = $('#commune').val();
            let street_no = $(this).val();
            let direction = address.concat(" ",street_no);

            if(country == '46' && commune != undefined && commune != "" && address.length > 5 && street_no.length > 0 && direction.length > 5)
            {
                $('#loading').fadeIn();
                $.get('{{ route("api.orders.recipient.normalize_address") }}',{
                    coummne: commune,
                    direction: direction,
                })
                .then(function(response){
                    if ( response.success == true ){
                        $('#zipcode').val(response.data.CodigoPostal);
                        $('#zipcode_response').empty().append("<p><b>According to your Coummune, your zipcode should be this</b></p><p><span style='color: red;'>zipcode: </span><span>"+response.data.CodigoPostal);
                        $('#loading').fadeOut();
                    }else{
                        $('#loading').fadeOut();
                        $('#zipcode_response').empty().append("<p style='color: red;'>"+response.message+"</p>");
                        toastr.error(response.message)
                    }
                }).catch(function(error){
                    console.log(error);
                })
            }
        });
    })


 
</script>
@endsection

