@extends('layouts.master')
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('setting.Edit Settings')</h4>
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
                            @can('update', App\Models\Setting::class)
                            <form action="{{ route('admin.settings.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('setting.Authorize ID')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" value="{{ old('AUTHORIZE_ID',setting('AUTHORIZE_ID')) }}" name="AUTHORIZE_ID" required placeholder="@lang('setting.Authorize ID')">
                                        <div class="help-block"></div>
                                        {{-- setting('AUTHORIZE_ID') --}}
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('setting.Authorize Key')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="AUTHORIZE_KEY" value="{{ old('AUTHORIZE_KEY',setting('AUTHORIZE_KEY')) }}" required placeholder="@lang('setting.Authorize Key')">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                
                                {{-- <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('setting.Stripe Key')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" value="{{ old('STRIPE_KEY',setting('STRIPE_KEY')) }}" name="STRIPE_KEY" placeholder="@lang('setting.Stripe Key')">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('setting.Stripe Secret')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="STRIPE_SECRET" value="{{ old('STRIPE_SECRET',setting('STRIPE_SECRET')) }}" placeholder="@lang('setting.Stripe Secret')">
                                        <div class="help-block"></div>
                                    </div>
                                </div> --}}

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('setting.Payment Gateway')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="PAYMENT_GATEWAY">
                                            <option value="AUTHORIZE" {{ setting('PAYMENT_GATEWAY') == 'AUTHORIZE' ? 'selected' : '' }}>Authorize</option>
                                            {{-- <option value="STRIPE" {{ setting('PAYMENT_GATEWAY') == 'STRIPE' ? 'selected' : '' }}>Stripe</option> --}}
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <hr>
                                <livewire:token-generator :user-id="auth()->id()"/>
                                
                                <h4>Commision Settings</h4>
                                <hr>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('user.Select Commision Type')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <select name="TYPE" class="form-control">
                                            <option value="" selected disabled hidden>@lang('user.Select Commision Type')</option>
                                            <option @if(setting('VALUE')) == 'flat') selected @endif value="flat">Flat</option>
                                            <option  @if(setting('VALUE')) == 'percentage') selected @endif value="percentage">Percentage</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                            
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('user.Commision Value')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="VALUE" value="{{ old('VALUE', setting('VALUE')) }}" class="form-control" id="VALUE"> 
                                    </div>  
                                    <div class="help-block"></div>
                                </div>
                                
                                <h4>Services Settings</h4>
                                <hr>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">USPS<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="usps">
                                                <input type="checkbox" name="usps" id="usps" @if(setting('usps', null, $adminId)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="usps_profit" step="0.01" min=0 class="form-control col-2" id="usps_profit" value="{{ setting('usps_profit', null, $adminId) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">UPS<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="ups">
                                                <input type="checkbox" name="ups"  id="ups" @if(setting('ups', null, $adminId)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="ups_profit" step="0.01" min=0 class="form-control col-2" id="ups_profit" value="{{ setting('ups_profit', null, $adminId) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">FedEx<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="ups">
                                                <input type="checkbox" name="fedex" id="fedex" @if(setting('fedex', null, \App\Models\User::ROLE_ADMIN)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="fedex_profit" step="0.01" min=0 class="form-control col-2" id="ups_profit" value="{{ setting('fedex_profit', null, $adminId) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">GSS<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="GSS">
                                                <input type="checkbox" name="gss" id="gss" @if(setting('gss', null, $adminId)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="gss_profit" step="0.01" min=0 class="form-control col-2" id="gss_profit" value="{{ setting('gss_profit', null, $adminId) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">GePS Prime<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Global E Parcel">
                                                <input type="checkbox" name="geps_service" id="geps_service" @if(setting('geps_service', null, \App\Models\User::ROLE_ADMIN)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Prime5<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Sweden Post - Prime5">
                                                <input type="checkbox" name="sweden_post" id="sweden_post" @if(setting('sweden_post', null, \App\Models\User::ROLE_ADMIN)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Post Plus<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="Post Plus">
                                                <input type="checkbox" name="post_plus" id="post_plus" @if(setting('post_plus', null, \App\Models\User::ROLE_ADMIN)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">GDE<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="GDE">
                                                <input type="checkbox" name="gde" id="gde" @if(setting('gde', null, \App\Models\User::ROLE_ADMIN)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Priority Mail (%) :</span>
                                            <input type="number" name="gde_pm_profit" step="0.01" min=0 class="form-control col-2" id="gde_pm_profit" value="{{ setting('gde_pm_profit', null, $adminId) }}">
                                            <span class="ml-3 mr-2 mt-2">First Class (%) :</span>
                                            <input type="number" name="gde_fc_profit" step="0.01" min=0 class="form-control col-2" id="gde_fc_profit" value="{{ setting('gde_fc_profit', null, $adminId) }}">
                                        </div>
                                    </div>
                                </div> 
                                <h4>Correios Settings</h4>
                                <hr>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right mt-4 h5" for="correios_api">Correios Api<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="form-check">
                                                <input class="form-check-input admin-api-settings" type="radio" name="correios_setting" id="correios_api" value="correios_api" @if(setting('correios_api', null, $adminId)) checked @endif>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right mt-4 h5" for="anjun_api">Anjun Api<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="form-check">
                                                <input class="form-check-input admin-api-settings" type="radio" name="correios_setting" id="anjun_api" value="anjun_api" @if(setting('anjun_api', null, $adminId)) checked @endif>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right mt-4 h5" for="bcn_api">BCN Api<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="form-check">
                                                <input class="form-check-input admin-api-settings" type="radio" name="correios_setting" id="bcn_api" value="bcn_api" @if(setting('bcn_api', null, $adminId)) checked @endif>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">    
                                    <label class="col-md-3 text-md-right mt-4 h5" for="china_anjun_api"> Anjun China Api<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="form-check">
                                                <input class="form-check-input admin-api-settings" type="radio" name="correios_setting" id="anjun_api" value="china_anjun_api" @if(setting('china_anjun_api', null, $adminId)) checked @endif>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('setting.Save Changes')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('setting.Reset')</button>
                                    </div>
                                </div>
                            </form>
                            @endcan
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
