@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
@section('page')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h4 class="mb-0">@lang('address.Add Address')</h4>
                            <p>@lang('address.corios-message')</p>
                        </div>
                        <a href="{{ route('admin.addresses.index') }}" class="pull-right btn btn-primary">@lang('address.Back to List') </a>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            @if( $errors->count() )
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('admin.addresses.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                {{-- <livewire:address.address-form /> --}}

                                <div>
                                    <div class="row mt-1">
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Type') <span class="text-danger">*</span></label>
                                                <select class="form-control" name="account_type" required placeholder="Account Type">
                                                    <option value="">@lang('address.Type')</option>
                                                    <option value="individual">Individual</option>
                                                    <option value="business">Business</option>
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.First Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="first_name" required placeholder="First Name">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Last Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="last_name" required placeholder="Last Name">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Email') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="email" required placeholder="Email">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Phone') <span class="text-danger">*(Formato internacional)</span></label>
                                                <input type="text" class="form-control" name="phone" required placeholder="+55123456789">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Address') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="address" required placeholder="Address"/>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Address')2</label>
                                                <input type="text" class="form-control"  placeholder=""  name="address2">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.street-no')</label>
                                                <input type="text" class="form-control" placeholder=""  name="street_no">
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <label>@lang('address.Country') <span class="text-danger">*</span></label>
                                                    <select id="country"  name="country_id" class="form-control selectpicker show-tick" data-live-search="true">
                                                        <option value="">Select @lang('address.Country')</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.UF') <span class="text-danger">*</span></label>
                                                <select name="state_id" id="state" class="form-control selectpicker show-tick" data-live-search="true" >
                                                    <option value="">Select @lang('address.UF')</option>
                                                    {{-- @foreach ($states as $state) --}}
                                                        {{-- <option value="{{ $state->id }}">{{ $state->code }}</option> --}}
                                                    {{-- @endforeach --}}
                                                </select>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.City') <span class="text-danger">*</span></label>
                                                <input type="text" name="city" class="form-control"  required placeholder="City"/>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>

                                        <div class="form-group col-12 col-sm-6 col-md-4">
                                            <div class="controls">
                                                <label>@lang('address.Zip Code')</label>
                                                <input type="text" name="zipcode" required class="form-control" placeholder="Zip Code"/>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        

                                        <div class="form-group col-12 col-sm-6 col-md-6">
                                            <div class="controls">
                                                <label>@lang('address.Tax') <span class="text-danger"></span></label>
                                                <textarea name="tax_id" required class="form-control" id="tax_id" cols="10" rows="5" placeholder="cpf / cnpj / cnic"></textarea>
                                                <div class="help-block"></div>
                                            </div>
                                        </div>
                                        
                                
                                        {{-- <div class="form-group col-12 col-sm-6 col-md-2">
                                            <div class="controls">
                                                <label>@lang('address.Default')</label>
                                                <input type="checkbox" name="default" value="1" class="">
                                                <div class="help-block"></div>
                                            </div>
                                        </div> --}}
                                    </div>
                                    {{-- @include('layouts.livewire.loading') --}}
                                </div>

                                
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('address.Save')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<x-get-state-list></x-get-state-list>
