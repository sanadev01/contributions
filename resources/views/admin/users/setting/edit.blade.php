@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<style>
    .dropdown .btn:not(.btn-sm):not(.btn-lg),
    .dropdown .btn:not(.btn-sm):not(.btn-lg).dropdown-toggle {
        background-color: white !important;
        border: 1px solid #ced4da;
        color: #495057 !important;
    }
</style>
@endsection
@section('page')
<section id="prealerts">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><b>{{ $user->name }}'s</b> Setting Edit | {{ $user->pobox_number }}</h4>
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
                        <form action="{{ route('admin.users.setting.store', $user->id) }}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="controls row align-items-center" style="margin-bottom: 2.25rem !important;">
                                <label class="col-md-3 text-md-right">@lang('user.Default Package')<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <select name="package_id" class="form-control selectpicker" data-live-search="true">
                                        <option value="" disabled hidden>@lang('user.Select Package')</option>
                                        @isset($packages)
                                        @foreach ($packages as $package)
                                        <option @if( $user->package_id == $package->id ) selected @endif value="{{ $package->id }}">{{ $package->name }}</option>
                                        @endforeach
                                        @endisset
                                    </select>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">@lang('user.Role')<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <select name="role_id" class="form-control">
                                        @isset($roles)
                                        @foreach ($roles as $role)
                                        <option @if( $user->role_id == $role->id ) selected @endif value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                        @endisset
                                    </select>
                                    <div class="help-block"></div>
                                </div>
                            </div>

                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">@lang('user.status')<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <select name="status" class="form-control">
                                        <option value="active" @if($user->status == 'active') selected @endif>Active</option>
                                        <option value="suspended" @if($user->status == 'suspended') selected @endif>Suspended</option>
                                    </select>
                                    <div class="help-block"></div>
                                </div>
                            </div>

                            <h3>Profit Package Settings</h3>
                            <hr>
                            <h4 class="ml-5">Multi Profit Services Settings</h4>
                            <livewire:profit.profit-setting :user_id="$user->id" />

                            <h3>Api Settings</h3>
                            <hr>

                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">Enable API<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <input type="checkbox" name="api_enabled" id="api_enabled" @if( $user->api_enabled == 1 ) checked @endif>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <livewire:token-generator :user_id="$user->id" />

                            <hr>
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">Enable Amazon API<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <input type="checkbox" name="amazon_api_enabled" id="api_enabled" @if( $user->amazon_api_enabled == 1 ) checked @endif>
                                    <div class="help-block"></div>
                                </div>
                            </div>

                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">Amazon Api key<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <textarea name="amazon_api_key" class="form-control">{{ $user->amazon_api_key }}</textarea>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <h3>Leve Settings</h3>
                            <hr>

                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">Market Place Name<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <input type="text" name="market_place_name" class="form-control" value="{{ $user->market_place_name }}">
                                    <div class="help-block"></div>
                                </div>
                            </div>

                            <h3>Profile Settings</h3>
                            <hr>
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">Email<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <input type="text" name="user_email" class="form-control" value="{{ $user->email }}">
                                    @error('user_email')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">Update Password<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <input type="password" name="password" class="form-control" value="">
                                    @error('password')
                                    <div class="help-block text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <h3>Affiliate Settings</h3>
                            <hr>

                            <h4 class="ml-5">Referrer Settings</h4>
                            <div class="controls row mb-1 align-items-center">
                                <label class="col-md-3 text-md-right">@lang('user.Referrer')<span class="text-danger"></span></label>
                                <div class="col-md-6">
                                    <select name="referrer_id[]" class="form-control selectpicker" multiple data-live-search="true">
                                        <option value="" disabled>@lang('user.Select Referrer')</option>
                                        @foreach ($users as $userRefferer)
                                        <option value="{{ $userRefferer->id }}" @if ($userRefferer->reffered_by == $user->id) selected @endif>{{ $userRefferer->name }} | {{ $userRefferer->pobox_number }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <hr>

                            <h4 class="ml-5">Commission Settings</h4>
                            <livewire:affiliate.commision-setting :user_id="$user->id" />
                            <hr>
                            <h2 class="ml-3">Waiver Fee Settings</h2>
                            <div class="container">
                                <div class="row">
                                    @if(setting('geps_service', null, \App\Models\User::ROLE_ADMIN))
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Global E Parcel">
                                                            <input type="checkbox" name="geps_service" id="geps_service" @if(setting('geps_service', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"> <label>GePS Prime<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if(setting('sweden_post', null, \App\Models\User::ROLE_ADMIN))
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Sweden Post - Prime5">
                                                            <input type="checkbox" name="sweden_post" id="sweden_post" @if(setting('sweden_post', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"> <label>Prime5<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if(setting('post_plus', null, \App\Models\User::ROLE_ADMIN))
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Post Plus">
                                                            <input type="checkbox" name="post_plus" id="post_plus" @if(setting('post_plus', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11">
                                                <label>Post Plus<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col align-items-center">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="vs-checkbox-con vs-checkbox-primary" title="Waive battery fee">
                                                        <input type="checkbox" name="battery" id="battery" @if(setting('battery', null, $user->id)) checked @endif>
                                                        <span class="vs-checkbox vs-checkbox-lg">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"> <label for="battery">Waive battery fee<span class="text-danger"></span></label>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row align-items-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="vs-checkbox-con vs-checkbox-primary" title="Waive perfume/aerosol/nail polish fee">
                                                        <input type="checkbox" name="perfume" id="perfume" @if(setting('perfume', null, $user->id)) checked @endif>
                                                        <span class="vs-checkbox vs-checkbox-lg">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11">
                                                <label>Waive perfume/aerosol/nail polish fee<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row  align-items-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="vs-checkbox-con vs-checkbox-primary" title="Insurance">
                                                        <input type="checkbox" name="insurance" id="perfume" @if(setting('insurance', null, $user->id)) checked @endif>
                                                        <span class="vs-checkbox vs-checkbox-lg">
                                                            <span class="vs-checkbox--check">
                                                                <i class="vs-icon feather icon-check"></i>
                                                            </span>
                                                        </span>
                                                        <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11">
                                                <label class="">Insurance<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Amazon Selling Partner">
                                                            <input type="checkbox" name="amazon_sp" id="amazon_sp" @if(setting('amazon_sp', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11">
                                                <label>Amazon Selling Partner<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Pasar Ex">                                                            
                                                            <input type="checkbox" name="pasarEx" id="pasarEx" @if(setting('pasarEx', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11">
                                                <label>PasarEx<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="ID Label Service">                                                            
                                                            <input type="checkbox" name="id_label_service" id="id_label_service" @if(setting('id_label_service', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11">
                                                <label>ID Label Service<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="USPS International">
                                                            <input type="checkbox" name="usps" id="usps" @if(setting('usps', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>USPS Int'l<span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Profit Percentage (%) :</span>
                                            </div>
                                            <div class="col-6"> <input type="number" name="usps_profit" step="0.01" min=0 max="100" class="form-control" id="usps_profit" value="{{ setting('usps_profit', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="ups">
                                                            <input type="checkbox" name="ups" id="ups" @if(setting('ups', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>UPS<span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Profit Percentage (%) :</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="ups_profit" step="0.01" min=0 max="100" class="form-control" id="ups_profit" value="{{ setting('ups_profit', null, $user->id) }}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="ups">
                                                            <input type="checkbox" name="fedex" id="fedex" @if(setting('fedex', null, $user->id)) checked @endif>

                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>FedEx<span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Profit Percentage (%) :</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="fedex_profit" step="0.01" min=0 max="100" class="form-control" id="ups_profit" value="{{ setting('fedex_profit', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="GSS">
                                                            <input type="checkbox" name="gss" id="gss" @if(setting('gss', null, $user->id)) checked @endif>

                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>USPS GSS Int'l<span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Profit Percentage (%) :</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" pattern="/^\d+(\.\d{2})?$/" name="gss_profit" step="0.01" min=0 max="100" class="form-control" id="gss_profit" value="{{ setting('gss_profit', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="volumetric_discount">
                                                            <input type="checkbox" name="volumetric_discount" id="volumetric_discount" @if(setting('volumetric_discount', null, $user->id)) checked @endif>

                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>Volumetric Discount Postal Service<span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Profit Percentage (%) :</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="discount_percentage" min=0 max="100" class="form-control" id="discount_percentage" value="{{ setting('discount_percentage', null, $user->id) }}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">

                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Tax Payment">
                                                            <input type="checkbox" name="tax" id="tax" @if(setting('tax', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label> Tax Payment<span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Profit Percentage (%) :</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="discount_percentage" class="form-control" min=0 max="100" id="discount_percentage" value="{{ setting('discount_percentage', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                </div>


                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="marketplace_checked">
                                                            <input type="checkbox" name="postal_volumetric_discount" id="postal_volumetric_discount" @if(setting('postal_volumetric_discount', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>Postal Discount <span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Profit Percentage (%) :</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="postal_discount_percentage" class="form-control" id="postal_discount_percentage" value="{{ setting('postal_discount_percentage', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="marketplace_checked">
                                                            <input type="checkbox" name="marketplace_checked" id="marketplace" @if(setting('marketplace_checked', null, $user->id)) checked @endif>

                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>Marketplace <span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Marketplace:</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="marketplace" class="form-control" id="marketplace" value="{{ setting('marketplace', null, $user->id) }}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                </div>

                            </div>
                            <hr>
                            <!-- gde input -->

                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <div class="row align-item-center">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="gde">
                                                            <input type="checkbox" name="gde" id="gde" @if(setting('gde', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"><label>GDE <span class="text-danger"></span></label></div>
                                        </div>
                                    </div>
                                    <div class="col ">
                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>Priority Mail (%):</span>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" name="gde_pm_profit" step="0.01" min=0 class="form-control" id="gde_pm_profit" value="{{ setting('gde_pm_profit', null, $user->id) }}">

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6 text-right">
                                                <span>First Class (%):</span>
                                            </div>
                                            <div class="col-6">

                                                <input type="number" name="gde_fc_profit" step="0.01" min=0 class="form-control " id="gde_fc_profit" value="{{ setting('gde_fc_profit', null, $user->id) }}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                </div>
                            </div>
                            <hr>
                            <!-- gde input end -->




                            <div class="container">
                                <div class="row">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="volumetric_discount">
                                                            <input type="checkbox" name="hd_express_volumetric_discount" id="hd_express_volumetric_discount" @if(setting('hd_express_volumetric_discount', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"> <label class="">Volumetric Discount Courier Service<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                    <div class="col"></div>
                                </div>
                                <div class="row mt-2 bg-light p-4">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-6">Discount %:</div>
                                            <div class="col-6"> <input type="number" name="hd_express_discount_percentage" max="100" class="form-control" id="hd_express_discount_percentage" value="{{ setting('hd_express_discount_percentage', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-6">Weight (%)</div>
                                            <div class="col-6"> <input class="form-control" step="0.01" min=0 type="number" max="100" name="weight" value="{{ setting('weight', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row">

                                            <div class="col-6">Length (%):</div>
                                            <div class="col-6"> <input type="number" name="length" class="form-control" max="100" id="length" step="0.01" min=0 type="number" value="{{ setting('length', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row">

                                            <div class="col-6">Width (%): </div>
                                            <div class="col-6"> <input type="number" name="width" class="form-control" id="width" step="0.01" min=0 max="100" type="number" value="{{ setting('width', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row">
                                            <div class="col-6">Height (%):</div>
                                            <div class="col-6"><input type="number" name="height" class="form-control" id="height" step="0.01" min=0 max="100" type="number" value="{{ setting('height', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hd Express Discount start -->
                                <div class="row">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-1">
                                                <div class="controls row mb-1 align-items-center">
                                                    <div class="input-group">
                                                        <div class="vs-checkbox-con vs-checkbox-primary" title="volumetric_discount">
                                                            <input type="checkbox" name="hd_express_volumetric_discount" id="hd_express_volumetric_discount" @if(setting('hd_express_volumetric_discount', null, $user->id)) checked @endif>
                                                            <span class="vs-checkbox vs-checkbox-lg">
                                                                <span class="vs-checkbox--check">
                                                                    <i class="vs-icon feather icon-check"></i>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-11"> <label class="">Hd Express Discount<span class="text-danger"></span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col"></div>
                                    <div class="col"></div>
                                </div>
                                <div class="row mt-2 bg-light p-4">
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-6">Discount %:</div>
                                            <div class="col-6"> <input type="number" name="hd_express_discount_percentage" max="100" class="form-control" id="hd_express_discount_percentage" value="{{ setting('hd_express_discount_percentage', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="row">
                                            <div class="col-6">Weight (%)</div>
                                            <div class="col-6"> <input class="form-control" step="0.01" min=0 type="number" max="100" name="weight" value="{{ setting('weight', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row">

                                            <div class="col-6">Length (%):</div>
                                            <div class="col-6"> <input type="number" name="length" class="form-control" max="100" id="length" step="0.01" min=0 type="number" value="{{ setting('length', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row">

                                            <div class="col-6">Width (%): </div>
                                            <div class="col-6"> <input type="number" name="width" class="form-control" id="width" step="0.01" min=0 max="100" type="number" value="{{ setting('width', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="row">
                                            <div class="col-6">Height (%):</div>
                                            <div class="col-6"><input type="number" name="height" class="form-control" id="height" step="0.01" min=0 max="100" type="number" value="{{ setting('height', null, $user->id) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Volumetric Discount Courier Service end -->


                            </div>
                    </div>

                    <hr>
                    <div class="container">
                        <div class="row mt-3">
                            <div class="col">
                                <div class="row">
                                    <div class="col-1">
                                        <div class="controls row mb-1 align-items-center">
                                            <div class="input-group">
                                                <div class="vs-checkbox-con vs-checkbox-primary" title="pay_tax_service">
                                                    <input type="checkbox" name="pay_tax_service" id="pay_tax_service" @if(setting('pay_tax_service', null, $user->id)) checked @endif>
                                                    <span class="vs-checkbox vs-checkbox-lg">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-11"> <label class="">Home Pay Convenience Fee <span class="text-danger"></span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row">
                                    <div class="col-4">
                                        <label class="text-right">PRC :<span class="text-danger"></span></label>
                                    </div>
                                    <div class="col-2">
                                        <input type="radio" name="is_prc_user" id="prcUserTrue" value="true" @if(setting('is_prc_user', null, $user->id)) checked @endif>

                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="row">
                                    <div class="col-4">
                                        <label class="text-right">Not PRC:<span class="text-danger"></span></label>
                                    </div>
                                    <div class="col-2">
                                        <input type="radio" name="is_prc_user" value="false" id="prcUserFalse" @if(!setting('is_prc_user', null, $user->id)) checked @endif>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="prc-dev" id="prcDev" style="display: none;">
                            <div class="row">
                                <div class="col"></div>
                                <div class="col p-3 bg-light">
                                    <div class="row">
                                        <div class="col-4">
                                            Flat Fee:
                                        </div>
                                        <div class="col-2">
                                            <input type="radio" name="prc_user_fee" value="flat_fee" @if(setting('prc_user_fee', null, $user->id)=="flat_fee") checked @endif>

                                        </div>
                                        <div class="col-6 ">
                                            <input type="number" name="prc_user_fee_flat" class="form-control" step="0.01" min="0" value="{{ setting('prc_user_fee_flat', null, $user->id) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col p-3 bg-light">
                                    <div class="row">
                                        <div class="col-4">
                                            <span> Variable Fee :&nbsp;
                                        </div>
                                        <div class="col-2">
                                            <input type="radio" name="prc_user_fee" value="variable_fee" @if(setting('prc_user_fee', null, $user->id)=="variable_fee") checked @endif></span>

                                        </div>
                                        <div class="col-6">
                                            <input type="number" name="prc_user_fee_variable" class="form-control" step="0.01" min="0" max="100" value="{{ setting('prc_user_fee_variable', null, $user->id) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="row m-3">
                            <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                    Save Changes
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
@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
    $(function() {
        $('.selectpicker').selectpicker();
    });

    document.addEventListener("DOMContentLoaded", function() {
        var prcUserTrue = document.getElementById('prcUserTrue');
        var prcUserFalse = document.getElementById('prcUserFalse');
        var prcDev = document.getElementById('prcDev');

        function togglePrcDev() {
            if (prcUserTrue.checked) {
                prcDev.style.display = 'block';
            } else {
                prcDev.style.display = 'none';
            }
        }
        togglePrcDev();
        prcUserTrue.addEventListener('change', togglePrcDev);
        prcUserFalse.addEventListener('change', togglePrcDev);
    });
</script>
@endsection