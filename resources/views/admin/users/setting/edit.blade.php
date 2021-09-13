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
                                            <input type="checkbox" name="battery" id="battery" @if( $user->battery == 1 ) checked @endif>
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
                                            <input type="checkbox" name="perfume" id="perfume" @if( $user->perfume == 1 ) checked @endif>
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
                                    <label class="col-md-3 text-md-right">USPS<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <div class="vs-checkbox-con vs-checkbox-primary" title="usps">
                                            <input type="checkbox" name="usps" id="usps" @if( $user->usps == 1 ) checked @endif>
                                            <span class="vs-checkbox vs-checkbox-lg">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="h3 mx-2 text-primary my-0 py-0"></span>
                                            </div>
                                            <span class="offset-2 mr-2 mt-2">Profit Percentage (%) :</span>
                                            <input type="number" name="api_profit" class="form-control col-2" id="api_profit" value="{{ $user->api_profit }}">
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