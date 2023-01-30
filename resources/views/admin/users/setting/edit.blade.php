@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<style>
    .dropdown .btn:not(.btn-sm):not(.btn-lg), .dropdown .btn:not(.btn-sm):not(.btn-lg).dropdown-toggle {
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
                        <h4 class="mb-0">Edit Settings</h4>
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
                                <livewire:profit.profit-setting :user_id="$user->id"  />

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
                                <livewire:affiliate.commision-setting :user_id="$user->id"  />
                                
                                <h3>Waiver Fee Settings</h3>
                                <hr>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right" for="battery">Waive battery fee<span class="text-danger"></span></label>
                                    <div class="col-md-6">
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
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Waive perfume/aerosol/nail polish fee<span class="text-danger"></span></label>
                                    <div class="col-md-6">
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
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Insurance<span class="text-danger"></span></label>
                                    <div class="col-md-6">
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
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Auto charge<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="usps">
                                                <input type="checkbox" name="charge" id="charge" @if(setting('charge', null, $user->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-4 mt-2">Amount :</span>
                                            <input type="number" name="charge_amount" step="0.01" min=0 class="form-control col-12 ml-5 " id="charge_amount" value="{{ setting('charge_amount', null, $user->id) }}">
                                      
                                            <span class="offset-2 mr-4 mt-2">When Balance less then:</span>
                                            <input type="number" name="charge_limit" step="0.01" min=0 class="form-control col-5" id="charge_limit" value="{{ setting('charge_limit', null, $user->id)??100 }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">USPS<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="usps">
                                                <input type="checkbox" name="usps" id="usps" @if(setting('usps', null, $user->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="usps_profit" step="0.01" min=0 class="form-control col-2" id="usps_profit" value="{{ setting('usps_profit', null, $user->id) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">UPS<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="ups">
                                                <input type="checkbox" name="ups"  id="ups" @if(setting('ups', null, $user->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="ups_profit" step="0.01" min=0 class="form-control col-2" id="ups_profit" value="{{ setting('ups_profit', null, $user->id) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">FedEx<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="ups">
                                                <input type="checkbox" name="fedex" id="fedex" @if(setting('fedex', null, $user->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="fedex_profit" step="0.01" min=0 class="form-control col-2" id="ups_profit" value="{{ setting('fedex_profit', null, $user->id) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">SinerLog<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="sinerlog">
                                                <input type="checkbox" name="sinerlog" id="sinerlog" @if(setting('sinerlog', null, $user->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>    
                                    </div>
                                </div>
                                @if(setting('geps_service', null, \App\Models\User::ROLE_ADMIN))
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">GePS Prime<span class="text-danger"></span></label>
                                    <div class="col-md-6">
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
                                @endif
                                @if(setting('sweden_post', null, \App\Models\User::ROLE_ADMIN))
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Prime5<span class="text-danger"></span></label>
                                    <div class="col-md-6">
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
                                @endif
                                {{-- <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Stripe<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Stripe">
                                            <input type="checkbox" name="stripe" id="stripe" @if(setting('stripe', null, $user->id)) checked @endif>
                                            <span class="vs-checkbox vs-checkbox-lg">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Volumetric Discount<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="volumetric_discount">
                                                <input type="checkbox" name="volumetric_discount" id="volumetric_discount" @if(setting('volumetric_discount', null, $user->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Discount Percentage (%) :</span>
                                            <input type="number" name="discount_percentage" class="form-control col-2" id="discount_percentage" value="{{ setting('discount_percentage', null, $user->id) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row align-items-center mt-2">
                                    <label class="col-md-3 text-md-right">Weight (%)<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div title="weight">
                                                <input class="form-control" step="0.01" min=0 type="number" name="weight" value="{{ setting('weight', null, $user->id) }}">
                                            </div>
                                            <span class="ml-4 mr-2 mt-2">Length (%): </span>
                                            <input type="number" name="length" class="form-control col-2" id="length" step="0.01" min=0 type="number" value="{{ setting('length', null, $user->id) }}">
                                            <span class="ml-4 mr-2 mt-2">Width (%): </span>
                                            <input type="number" name="width" class="form-control col-2" id="width" step="0.01" min=0 type="number" value="{{ setting('width', null, $user->id) }}">
                                            <span class="ml-4 mr-2 mt-2">Height (%): </span>
                                            <input type="number" name="height" class="form-control col-2" id="height" step="0.01" min=0 type="number" value="{{ setting('height', null, $user->id) }}">
                                        </div>    
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right" for="tax">Tax Payment<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary" title="Tax Payment">
                                            <input type="checkbox" name="tax" id="tax" @if(setting('tax', null, $user->id)) checked @endif>
                                            <span class="vs-checkbox vs-checkbox-lg">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Marketplace<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="marketplace_checked">
                                                <input type="checkbox" name="marketplace_checked" id="marketplace" @if(setting('marketplace_checked', null, $user->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Marketplace :</span>
                                            <input type="text" name="marketplace" class="form-control col-5" id="marketplace" value="{{ setting('marketplace', null, $user->id) }}">
                                        </div>    
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">Pay Tax Service<span class="text-danger"></span></label>
                                    <div class="col-md-6">
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
                                
                                <div class="row mt-1">
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
    $(function () {
        $('.selectpicker').selectpicker();
    });
</script>
@endsection