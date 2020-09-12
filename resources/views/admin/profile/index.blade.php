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
                                    <label class="col-md-3 text-md-right">@lang('profile.Address')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="address" value="{{ old('name',auth()->user()->address) }}" placeholder="@lang('profile.Address')"/>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Address')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="address2" value="{{ old('name',auth()->user()->address2) }}"  placeholder="@lang('profile.Address') 2"/>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Street No')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="street_no" value="{{ old('name',auth()->user()->street_no) }}" placeholder="@lang('profile.Street No')"/>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Country')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <select name="country_id" class="form-control">
                                            <option value="" selected disabled hidden>@lang('profile.Select Country')</option>
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
                                    <label class="col-md-3 text-md-right">@lang('profile.State')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <select name="state_id" class="form-control">
                                            <option value="" selected disabled hidden>@lang('profile.Select State') </option>
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
                                    <label class="col-md-3 text-md-right">@lang('profile.City')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="city" value="{{ old('name',auth()->user()->city) }}"  placeholder="@lang('profile.City')"/>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.Zipcode')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="zipcode" value="{{ old('name',auth()->user()->zipcode) }}"  placeholder="@lang('profile.Zipcode')"/>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.cpf / cnpj / cnic')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <textarea name="tax_id"  class="form-control" id="tax_id" cols="10" rows="5" placeholder="cpf / cnpj / cnic">{{ old('name',auth()->user()->tax_id) }}"</textarea>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                <div class="controls row mb-1 align-items-center">
                                    <label class="col-md-3 text-md-right">@lang('profile.language')<span class="text-danger"></span></label>
                                    <div class="col-md-6">
                                        <select name="locale" class="form-control">
                                            <option value="" selected disabled hidden>@lang('profile.Select Language')</option>
                                            <option @if( auth()->user()->locale == 'en' ) selected @endif value="en">English</option>
                                        </select>
                                        <div class="help-block"></div>
                                    </div>
                                </div>

                                {{-- @if( auth()->user()->isAdmin() )
                                <h3>Pobox Information</h3>
                                <hr>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">Pobox Address <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <textarea type="text" class="form-control" name="pobox_address" placeholder="Pobox Address">{{ old('pobox_address',$pobox->address) }}</textarea>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">Pobox City <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="pobox_city" placeholder="Pobox City" value="{{ old('pobox_city',$pobox->city) }}">
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">State <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="pobox_state">
                                                <option value="">Select State</option>
                                                @foreach (countries()->where('code','US')->first()->states as $state)
                                                <option value="{{ $state->code }}" {{ old('pobox_state',$pobox->state)==$state->code ? 'selected':'' }}>{{ $state->code }}</option>
                                                @endforeach
                                            </select>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">Country <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <select class="form-control" name="pobox_country" placeholder="Pobox Address">
                                                <option value="US" {{ old('pobox_country',$pobox->country) == 'us' ? 'selected':'' }}>US</option>
                                            </select>
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">ZipCode <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="pobox_zipcode" placeholder="ZipCode" value="{{ old('pobox_zipcode',$pobox->zipcode) }}" />
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="controls row mb-1 align-items-center">
                                        <label class="col-md-3 text-md-right">Phone <span class="text-danger">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="pobox_phone" placeholder="Phone" value="{{ old('pobox_phone',$pobox->phone) }}" />
                                            <div class="help-block"></div>
                                        </div>
                                    </div>
                                @endif --}}


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
