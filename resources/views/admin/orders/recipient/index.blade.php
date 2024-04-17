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
        @if($order->sender_country_id == 46)
        <div class="controls d-flex mb-1">
            <div>
                <div class="vs-checkbox-con vs-checkbox-primary" title="Insurance">
                    <input type="radio" name="service" value="postal_service" id="postal_service"  required @if( (optional($order->recipient)->commune_id == null && $order->recipient != null) || old('service') == 'postal_service') checked @endif>
                    <span class="vs-checkbox vs-checkbox-lg">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="h3 mx-2 text-primary my-0 py-0">Postal Service</span>
                </div>
            </div>
            <div class="ml-3">
                <div class="vs-checkbox-con vs-checkbox-primary" title="Insurance">
                    <input type="radio" name="service" value="courier_express" id="courier_express" required @if( optional($order->recipient)->commune_id != null || old('service') == 'courier_express') checked @endif>
                    <span class="vs-checkbox vs-checkbox-lg">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="h3 mx-2 text-primary my-0 py-0">Courier Express</span>
                </div>
            </div>
        </div>
        @elseif($order->sender_country_id != 46)
        <input type="hidden" name="service" value="postal_service" id="postal_service">
        @endif
        
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
                            <option value="individual" {{ old('account_type', optional($order->recipient)->account_type) == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="business" {{ old('account_type', optional($order->recipient)->account_type) == 'business' ? 'selected' : '' }}>Business</option>
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
                        <label>@lang('address.Phone') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" value="{{old('phone',optional($order->recipient)->phone)}}" placeholder="+55123456789">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label id="label_address">@lang('address.Address') <span class="text-danger">*</span></label>
                        <label id="label_chile_address" style="display: none;">@lang('address.Chile Address')<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address" name="address" value="{{old('address',optional($order->recipient)->address)}}" maxlength="38" required placeholder="@lang('address.Address')"/>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Address2')</label>
                        <input type="text" class="form-control"  placeholder="@lang('address.Address2')" value="{{old('address2',optional($order->recipient)->address2)}}"  name="address2">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="form-group">
                        <div class="controls">
                            <label>@lang('address.Country') <span class="text-danger">*</span></label>
                            <select id="country"  name="country_id" class="form-control selectpicker show-tick" data-live-search="true">
                                <option value="">Select @lang('address.Country')</option>
                                @foreach (countries() as $country)
                                    <option {{ old('country_id',optional($order->recipient)->country_id) == $country->id ? 'selected' : '' }} value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            <div class="help-block" id="country_message"></div>
                        </div>
                    </div>
                </div> 
                    <div class="controls form-group col-12 col-sm-6 col-md-4" id="div_state">
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
                    <div class="controls form-group col-12 col-sm-6 col-md-4" id="div_region" style="display: none">
                        <label>Regions<span class="text-danger">*</span></label>
                        <select name="region" id="region" class="form-control selectpicker show-tick" data-live-search="true" data-value="{{ old('region', optional($order->recipient)->region) }}">
                            <option value="">Select Region</option>
                        </select>
                        <div class="help-block"></div>
                    </div> 
                    <div class="form-group col-12 col-sm-6 col-md-4" id="state_input"  style="display: none">
                        <div class="controls">
                            <label>@lang('address.State') <span class="text-danger">*</span></label>
                            <input type="text" name="region" id="region_input" value="{{old('region',optional($order->recipient)->region)}}" class="form-control" placeholder="State"/>
                            <div class="help-block"></div>
                        </div>
                    </div>
                {{-- <div class="form-group col-12 offset-4">
                    <div class="controls">
                        <div class="help-block" id="regions_response">
                        </div>
                    </div>
                </div> --}}
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls" id="div_city">
                        <label>@lang('address.City') <span class="text-danger">*</span></label>
                        <input type="text" id="city" name="city" value="{{old('city',optional($order->recipient)->city)}}" class="form-control" placeholder="City"/>
                        <div class="help-block"></div>
                    </div>
                    {{-- Chile Communes --}}
                    <div class="controls" id="div_communes" style="display: none">
                        <label>Communes <span class="text-danger">*</span></label>
                        <input type="text" id="city" name="city" value="{{old('city',optional($order->recipient)->city)}}" class="form-control" placeholder="City"/>
                       
                        <!-- <select name="city" id="commune" class="form-control selectpicker show-tick" data-live-search="true" data-value="{{ old('city', optional($order->recipient)->city) }}" data-commune="{{ old('commune_id', optional($order->recipient)->commune_id) }}">
                            <option value="">Select Commune</option>
                        </select> -->
                        <div class="help-block"></div>
                    </div>
                    <div class="controls">
                        <div class="help-block" id="communes_response" style="display: none">
                        </div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4" id="div_street_number">
                    <div class="controls">
                        <label>@lang('address.Street No') <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="@lang('address.Street No')" value="{{old('street_no',optional($order->recipient)->street_no)}}"  name="street_no" id="street_no">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group col-12 col-sm-6 col-md-4">
                    <div class="controls">
                        <label>@lang('address.Zip Code') <span class="text-danger">*</span></label>
                        <input type="text" name="zipcode"  id="zipcode" value="{{ cleanString(old('zipcode',optional($order->recipient)->zipcode)) }}" class="form-control" placeholder="Zip Code"/>
                        <div class="help-block"></div>
                    </div>
                </div>

                <div class="form-group col-12 col-sm-6 col-md-4" id="cpf" style="display: none">
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
                    <a href="{{ route('admin.orders.sender.index',$order->encrypted_id) }}" role="menuitem">@lang('orders.recipient.Previous')</a>
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

@include('admin.orders.recipient.script')

@endsection

