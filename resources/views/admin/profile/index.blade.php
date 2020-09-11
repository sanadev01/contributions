@extends('layouts.master')
@section('page')
    <section id="prealerts">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">@lang('profile.Edit Profile')</h4>
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
                            <form action="{{ route('admin.profile.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @if( auth()->user()->isBusinessAccount() )
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">Company Name <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="name" value="{{ old('name',auth()->user()->name) }}" placeholder="Company Name">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                @else
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Fist Name') <span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" value="{{ old('name',auth()->user()->name) }}" placeholder="">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Last Name') <span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name',auth()->user()->last_name) }}" placeholder="">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                @endif
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Phone') <span class="text-danger">* (Formato Internacional)</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone',auth()->user()->phone) }}" placeholder="+55123456789">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Password')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="password" placeholder="">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Confirm Password')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="password" class="form-control" name="password_confirmation" placeholder="">
                                        <div class="help-block"></div>
                                    </div>
                                </div>



                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.language')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <select name="locale" class="form-control">
                                            <option value="" selected disabled hidden>Select Language</option>
                                            <option @if( auth()->user()->locale == 'en' ) selected @endif value="en">English</option>
                                            <option @if( auth()->user()->locale == 'pt' ) selected @endif value="pt">Portuguese</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <h3>Pobox Information</h3>
                                <hr>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">Pobox Address <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <textarea type="text" class="form-control" readonly placeholder="Pobox Address">{{ old( 'pobox_number',auth()->user()->pobox_number) }}</textarea>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>

                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.address')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="address" value="{{ old('name',auth()->user()->address) }}" placeholder="Address"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.address2')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="address2" value="{{ old('name',auth()->user()->address2) }}"  placeholder="Address 2"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.street_no')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="street_no" value="{{ old('name',auth()->user()->street_no) }}" placeholder="Street No"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.country')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <select name="country_id" class="form-control">
                                                <option value="" selected disabled hidden>Select Country</option>
                                                @isset($countries)
                                                    @foreach ($countries as $country)
                                                        <option @if( auth()->user()->country_id == $country->id ) selected @endif  value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.state')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <select name="state_id" class="form-control">
                                                <option value="" selected disabled hidden>Select State</option>
                                                @isset($states)
                                                    @foreach ($states as $state)
                                                        <option @if( auth()->user()->state_id == $state->id ) selected @endif value="{{ $state->id }}">{{ $state->code }}</option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.city')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="city" value="{{ old('name',auth()->user()->city) }}"  placeholder="City"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.zipcode')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="zipcode" value="{{ old('name',auth()->user()->zipcode) }}"  placeholder="zipcode"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.CPF') / @lang('profile.CNPJ')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="tax_id" value="{{ old('name',auth()->user()->tax_id) }}"  placeholder="CPF / CNPJ"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>


                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('profile.Save')
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
