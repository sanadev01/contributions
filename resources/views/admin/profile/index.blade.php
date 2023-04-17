@extends('layouts.master')
@section('css')
<link rel="stylesheet" href="{{ asset('app-assets/select/css/bootstrap-select.min.css') }}">
@endsection
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
                                        <label class="col-md-3 text-md-right">@lang('profile.Company Name') <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="name" value="{{ old('name',auth()->user()->name) }}" placeholder="@lang('profile.Company Name')">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                @else
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.First Name') <span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="name" value="{{ old('name',auth()->user()->name) }}" placeholder="@lang('profile.First Name')">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Last Name') <span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="last_name" value="{{ old('last_name',auth()->user()->last_name) }}" placeholder="@lang('profile.Last Name')">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                @endif
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Phone') <span class="text-danger">@lang('profile.* (Format International)')</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="phone" value="{{ old('phone',auth()->user()->phone) }}" placeholder="+55123456789">
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Email') <span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="email" value="{{ old('email',auth()->user()->email) }}" placeholder="user@user.com">
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
                                            <option value="" selected disabled hidden>@lang('profile.Select Language')</option>
                                            <option @if( auth()->user()->locale == 'en' ) selected @endif value="en">English</option>
                                            <option @if( auth()->user()->locale == 'pt' ) selected @endif value="pt">Portuguese</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Profile Picture')<span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="file" accept="image/*" class="form-control" name="image" placeholder="">
                                        <div class="help-block"></div>
                                        <img src="{{ auth()->user()->getImage() }}" style="width: 100px; height:100px;" alt="">
                                    </div>
                                </div>

                                <h3>@lang('profile.Pobox Information')</h3>
                                <hr>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.Pobox Address')<span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <textarea type="text" class="form-control" readonly placeholder="@lang('profile.Pobox Address')">{{ old( 'pobox_number',auth()->user()->pobox_number) }}</textarea>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>

                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.Address')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="address" value="{{ old('address',auth()->user()->address) }}" placeholder="@lang('profile.Address')"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.Address')2<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="address2" value="{{ old('address2',auth()->user()->address2) }}"  placeholder="@lang('profile.Address') 2"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.Street No')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="street_no" value="{{ old('street_no',auth()->user()->street_no) }}" placeholder="@lang('profile.Street No')"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.Country')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <select id="country" name="country_id" class="form-control selectpicker show-tick" data-live-search="true">
                                                <option value="" selected disabled hidden>@lang('profile.Select Country')</option>
                                                    @foreach (countries() as $country)
                                                        <option @if( auth()->user()->country_id == $country->id ) selected @endif  value="{{ $country->id }}">{{ $country->name }}</option>
                                                    @endforeach
                                            </select>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.State')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <select id="state" name="state_id" class="form-control selectpicker show-tick" data-live-search="true">
                                                <option value="" selected disabled hidden>@lang('profile.Select State')</option>
                                                @foreach (states(auth()->user()->country_id) as $state)
                                                    <option value="{{ $state->id }}" {{ auth()->user()->state_id == $state->id ? 'selected': '' }}> {{ $state->code }} </option>
                                                @endforeach
                                            </select>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.City')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="city" value="{{ old('city',auth()->user()->city) }}"  placeholder="@lang('profile.City')"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">@lang('profile.Zipcode')<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="zipcode" value="{{ old('zipcode',auth()->user()->zipcode) }}"  placeholder="@lang('profile.Zipcode')"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
    
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">CPF / CNPJ<span class="text-danger"></span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="tax_id" value="{{ old('tax_id',auth()->user()->tax_id) }}"  placeholder="CPF / CNPJ"/>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <div class="offset-3">
                                            <div class="input-group ml-3">
                                                <div class="vs-checkbox-con vs-checkbox-primary" title="Auto charge">
                                                    <input type="checkbox" name="auto_charge" id="auto_charge" @if(setting('auto_charge', null, auth()->user()->id)) checked @endif>
                                                    <span class="vs-checkbox vs-checkbox-lg">
                                                        <span class="vs-checkbox--check">
                                                            <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="col-md-6 font-medium-1 font-weight-bold" for="auto_charge">@lang('profile.payment permission')<span class="text-danger"></span></label>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <div class="offset-3 form-check form-check-inline">
                                            <div class="vs-checkbox-con vs-checkbox-primary ml-3" title="Parcel Return to Origin">
                                                <input type="checkbox" name="return_origin" id="returnParcel" @if(setting('return_origin', null, auth()->user()->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <label class="form-check-label font-medium-1 font-weight-bold mt-2" for="dispose_all">Return All Parcels on My Account Cost<span class="text-danger"></span></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <div class="vs-checkbox-con vs-checkbox-primary ml-3" title="Parcel Return to Origin">
                                                <input type="checkbox" name="dispose_all" id="disposeAll" @if(setting('dispose_all', null, auth()->user()->id)) checked @endif>
                                                <span class="vs-checkbox vs-checkbox-lg">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                            </div>
                                            <label class="form-check-label font-medium-1 font-weight-bold mt-2" for="dispose_all">Dispose All Authorized<span class="text-danger"></span></label>
                                        </div>
                                    </div>
                                <div class="row mt-1">
                                    <div class="col-12 d-flex flex-sm-row flex-column justify-content-end mt-1">
                                        <button type="submit" class="btn btn-primary glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light">
                                            @lang('profile.Save')
                                        </button>
                                        <button type="reset" class="btn btn-outline-warning waves-effect waves-light">@lang('profile.Reset')</button>
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
    <script src="{{ asset('app-assets/select/js/bootstrap-select.min.js') }}"></script>
    <script>
        $('#returnParcel').change(function() {
            if($(this).is(":checked")){
            $('#disposeAll').prop('checked', false);
            }    
        });
        $('#disposeAll').change(function() {
            if($(this).is(":checked")){
            $('#returnParcel').prop('checked', false);
            }    
        });
    </script>
    @include('layouts.states-ajax')
@endsection