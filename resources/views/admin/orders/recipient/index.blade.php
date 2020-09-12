@extends('admin.orders.layouts.wizard')

@section('wizard-form')
<form action="{{ route('admin.orders.recipient.store',$order) }}" method="POST" class="wizard">
    @csrf
    <div class="content clearfix">
        <!-- Step 1 -->
        <h6 id="steps-uid-0-h-0" tabindex="-1" class="title current">Step 1</h6>
        <fieldset id="steps-uid-0-p-0" role="tabpanel" aria-labelledby="steps-uid-0-h-0" class="body current" aria-hidden="false">
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
                            <select name="country_id" class="form-control selectpicker show-tick" data-live-search="true">
                                @foreach (countries() as $country)
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
                        <select name="state_id" class="form-control selectpicker show-tick" data-live-search="true" >
                            <option value="">Select @lang('address.UF')</option>
                            @foreach (states() as $state)
                                <option value="{{ $state->id }}">{{ $state->code }}</option>
                            @endforeach
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
                        <input name="tax_id" required class="form-control" id="tax_id" value="{{ old('tax_id') }}" placeholder="cpf / cnpj / cnic"/>
                        <div class="help-block"></div>
                    </div>
                </div>
                
            </div>
        </fieldset>
    </div>
    <div class="actions clearfix">
        <ul role="menu" aria-label="Pagination">
            <li class="disabled" aria-disabled="true">
                <a href="{{ route('admin.orders.sender.index',$order) }}" role="menuitem">Previous</a>
            </li>
            <li aria-hidden="false" aria-disabled="false">
                <button class="btn btn-primary">Next</button>
            </li>
        </ul>
    </div>
</form>
@endsection