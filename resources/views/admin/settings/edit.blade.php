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
                                        <input type="text" class="form-control" value="{{ old('STRIPE_KEY',setting('STRIPE_KEY')) }}" name="STRIPE_KEY" required placeholder="@lang('setting.Stripe Key')">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('setting.Stripe Secret')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="STRIPE_SECRET" value="{{ old('STRIPE_SECRET',setting('STRIPE_SECRET')) }}" required placeholder="@lang('setting.Stripe Secret')">
                                        <div class="help-block"></div>
                                    </div>
                                </div> --}}

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('setting.Payment Gateway')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="PAYMENT_GATEWAY">
                                            <option value="AUTHORIZE" {{ setting('PAYMENT_GATEWAY') == 'AUTHORIZE' ? 'selected' : '' }}>Authorize</option>
                                            <option value="STRIPE" {{ setting('PAYMENT_GATEWAY') == 'STRIPE' ? 'selected' : '' }}>Stripe</option>
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
                                <h4>Correios Settings</h4>
                                <hr>
                                <div class="controls row mb-1 align-items-center ml-5">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="correios_setting" id="correios_api" value="correios_api" @if(!setting('anjun_api', null, $adminId)) checked @endif>
                                        <label class="form-check-label h2" for="correios_api">
                                          Correios Api
                                        </label> 
                                      </div>
                                      <div class="form-check ml-2">
                                        <input class="form-check-input" type="radio" name="correios_setting" id="anjun_api" value="anjun_api" @if(setting('anjun_api', null, $adminId)) checked @endif>
                                        <label class="form-check-label h2" for="anjun_api">
                                          Anjun Api
                                        </label>
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
